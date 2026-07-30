<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * One-shot copy of the MySQL database into MongoDB.
 *
 * Two things matter for the application to keep working afterwards:
 *
 * 1. Primary keys are preserved exactly. Every row's integer id becomes
 *    the document's _id, so the foreign keys already stored in other
 *    tables (persid, appl_id, year_id, ...) still point at the right
 *    documents without being rewritten.
 *
 * 2. Column types are preserved. PDO hands most values back as strings;
 *    left that way, sum('amount_granted') would return 0 in MongoDB
 *    because it ignores non-numeric values. Numeric columns are
 *    therefore cast from the schema, while datetimes stay strings in
 *    MySQL's 'Y-m-d H:i:s' format — which is what the application
 *    itself writes (date('Y-m-d H:i:s')) and compares against, and
 *    which sorts chronologically under a plain string comparison.
 */
class ImportMysqlToMongo extends Command
{
    protected $signature = 'mongo:import
        {--source=mysql : The connection to read from}
        {--fresh : Drop each destination collection before importing}';

    protected $description = 'Copy every table from MySQL into MongoDB, preserving ids and column types';

    private const INT_TYPES = ['int', 'bigint', 'smallint', 'mediumint', 'tinyint'];
    private const FLOAT_TYPES = ['decimal', 'float', 'double', 'newdecimal'];

    public function handle(): int
    {
        $source = DB::connection($this->option('source'));
        $target = DB::connection('mongodb');

        $tables = $this->tables($source);

        if ($tables === []) {
            $this->error('No tables found on the source connection.');

            return self::FAILURE;
        }

        $summary = [];

        foreach ($tables as $table) {
            $casts = $this->casts($source, $table);
            $collection = $target->getCollection($table);

            if ($this->option('fresh')) {
                $collection->drop();
            }

            $expected = $source->table($table)->count();
            $imported = 0;
            $maxId = 0;

            $source->table($table)->orderBy('id')->chunk(500, function ($rows) use ($collection, $casts, &$imported, &$maxId) {
                $documents = [];

                foreach ($rows as $row) {
                    $document = $this->toDocument((array) $row, $casts);
                    $maxId = max($maxId, $document['_id']);
                    $documents[] = $document;
                }

                $collection->insertMany($documents);
                $imported += count($documents);
            });

            // Seed the auto-increment counter so ids issued from here on
            // continue past the highest imported id rather than colliding.
            $target->getCollection('counters')->updateOne(
                ['_id' => $table],
                ['$set' => ['seq' => $maxId]],
                ['upsert' => true],
            );

            $actual = $collection->countDocuments();
            $summary[] = [$table, $expected, $imported, $actual, $expected === $actual ? 'ok' : 'MISMATCH'];
        }

        $this->table(['collection', 'mysql rows', 'inserted', 'mongo docs', 'status'], $summary);

        $mismatched = array_filter($summary, fn ($row) => $row[4] !== 'ok');

        if ($mismatched !== []) {
            $this->error(count($mismatched).' collection(s) did not match the source row count.');

            return self::FAILURE;
        }

        $this->info('All '.array_sum(array_column($summary, 3)).' rows imported and verified.');

        return self::SUCCESS;
    }

    /**
     * Every base table in the source schema, in a stable order.
     *
     * @return list<string>
     */
    private function tables($source): array
    {
        $rows = $source->select(
            'SELECT TABLE_NAME AS name FROM information_schema.TABLES
             WHERE TABLE_SCHEMA = ? AND TABLE_TYPE = "BASE TABLE" ORDER BY TABLE_NAME',
            [$source->getDatabaseName()],
        );

        return array_map(fn ($row) => $row->name, $rows);
    }

    /**
     * Column name => native PHP type to cast that column to.
     *
     * @return array<string, string>
     */
    private function casts($source, string $table): array
    {
        $rows = $source->select(
            'SELECT COLUMN_NAME AS name, DATA_TYPE AS type FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?',
            [$source->getDatabaseName(), $table],
        );

        $casts = [];

        foreach ($rows as $row) {
            if (in_array($row->type, self::INT_TYPES, true)) {
                $casts[$row->name] = 'int';
            } elseif (in_array($row->type, self::FLOAT_TYPES, true)) {
                $casts[$row->name] = 'float';
            }
        }

        return $casts;
    }

    /**
     * Turn one MySQL row into the document that replaces it.
     *
     * @param  array<string, mixed>  $row
     * @param  array<string, string> $casts
     * @return array<string, mixed>
     */
    private function toDocument(array $row, array $casts): array
    {
        $document = [];

        foreach ($row as $column => $value) {
            if ($value !== null && isset($casts[$column])) {
                $value = $casts[$column] === 'int' ? (int) $value : (float) $value;
            }

            $document[$column] = $value;
        }

        // MongoDB's key is _id; the package addresses it as 'id' from
        // Eloquent, so nothing in the application has to change.
        $document['_id'] = $document['id'];
        unset($document['id']);

        return $document;
    }
}
