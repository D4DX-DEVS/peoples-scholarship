<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Creates the index the People-ERP bridge relies on for correctness.
 *
 * The bridge is idempotent because it looks an application up by external_id
 * before inserting. That check alone is not enough: two pushes for the same
 * application arriving at once — the submit hook and the retry sweep, say —
 * would both find nothing and both insert. A unique index makes the second
 * insert fail instead, which is the behaviour that actually prevents
 * duplicates.
 *
 * Sparse, because every application entered through the public form has no
 * external_id and a plain unique index would reject all but the first of them.
 *
 * Safe to run repeatedly; MongoDB ignores a request to create an index that
 * already exists with the same options.
 */
class CreateBridgeIndexes extends Command
{
    protected $signature = 'bridge:indexes';

    protected $description = 'Create the MongoDB indexes used by the People-ERP bridge';

    public function handle(): int
    {
        DB::connection('mongodb')
            ->getCollection('applications')
            ->createIndex(
                ['external_id' => 1],
                ['unique' => true, 'sparse' => true, 'name' => 'external_id_unique'],
            );

        $this->info('Created index external_id_unique on applications.');

        return self::SUCCESS;
    }
}
