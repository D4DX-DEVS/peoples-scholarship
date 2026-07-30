<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MongoDB\BSON\Regex;

/**
 * Server-side paging, searching and sorting for the admin tables.
 *
 * The tables show columns that live in other collections — an
 * application's row shows the applicant's name, their unit, area and
 * district — and MongoDB cannot join in an ordinary query. Searching or
 * sorting on those columns therefore has to happen in an aggregation
 * pipeline with $lookup.
 *
 * The pipeline is used only to decide *which* documents belong on the
 * page, and returns their ids plus the row counts. Rendering then loads
 * those handful of models through Eloquent with eager loading, so the
 * existing Blade markup keeps working unchanged and there is no second
 * copy of it to maintain.
 *
 * $facet lets the page of ids and the filtered total be computed in one
 * round trip, which matters when the database is ~64ms away.
 */
class DataTableQuery
{
    /**
     * @param string                     $collection base collection name
     * @param array<string, array>       $lookups    alias => [from, localField, foreignField]
     * @param array<int, array>          $columns    DataTables column index => [path, searchable, orderable]
     * @param array<string, mixed>       $baseMatch  filter always applied (e.g. only granted rows)
     * @param array<string, string>      $filters    request key => document path, for the dropdown filters
     */
    public function __construct(
        private string $collection,
        private array $lookups = [],
        private array $columns = [],
        private array $baseMatch = [],
        private array $filters = [],
    ) {
    }

    /**
     * Resolve one page.
     *
     * @return array{ids: list<int>, total: int, filtered: int}
     */
    public function page(Request $request): array
    {
        $start  = max(0, (int) $request->input('start', 0));
        $length = (int) $request->input('length', 10);
        $match  = $this->buildMatch($request);

        // -1 is DataTables' "show everything", which the export paths use.
        $slice = $length < 0
            ? [['$skip' => $start]]
            : [['$skip' => $start], ['$limit' => $length]];

        $pipeline = array_merge(
            $this->baseMatch === [] ? [] : [['$match' => $this->baseMatch]],
            $this->lookupStages(),
            $match === [] ? [] : [['$match' => $match]],
            [[
                '$facet' => [
                    'rows'  => array_merge([['$sort' => $this->buildSort($request)]], $slice, [['$project' => ['_id' => 1]]]),
                    'count' => [['$count' => 'n']],
                ],
            ]],
        );

        // MySQL's collation was case-insensitive, so "Abdul Basith" sorted
        // before "ABDUL HASEEB". MongoDB sorts by byte value by default,
        // which puts every capital ahead of every lowercase letter and makes
        // an alphabetical list of mixed-case names look shuffled. strength 2
        // compares base letters only, matching the old ordering.
        $result = $this->collection()
            ->aggregate($pipeline, ['collation' => ['locale' => 'en', 'strength' => 2]])
            ->toArray();
        $facet  = $result[0] ?? null;

        $ids      = [];
        $filtered = 0;

        if ($facet !== null) {
            foreach ($facet['rows'] as $row) {
                $ids[] = $row['_id'];
            }
            $filtered = isset($facet['count'][0]) ? (int) $facet['count'][0]['n'] : 0;
        }

        return [
            'ids'      => $ids,
            'filtered' => $filtered,
            'total'    => $this->collection()->countDocuments($this->baseMatch),
        ];
    }

    /**
     * $lookup each related collection and flatten it, so later stages can
     * match and sort on a plain path such as "person.personname".
     *
     * @return list<array>
     */
    private function lookupStages(): array
    {
        $stages = [];

        foreach ($this->lookups as $alias => $spec) {
            $stages[] = ['$lookup' => [
                'from'         => $spec['from'],
                'localField'   => $spec['localField'],
                'foreignField' => $spec['foreignField'] ?? '_id',
                'as'           => $alias,
            ]];

            // Take the first match rather than $unwind. $unwind would emit
            // one row per match, so a hasOne whose data has a duplicate —
            // area 65 has two areaauths rows — would appear twice and push
            // the filtered count above the total. Eloquent's belongsTo and
            // hasOne both resolve to a single record, and $arrayElemAt does
            // the same, while leaving rows with no match in place (the field
            // simply becomes missing, as a LEFT JOIN left it null).
            $stages[] = ['$addFields' => [
                $alias => ['$arrayElemAt' => ['$'.$alias, 0]],
            ]];
        }

        return $stages;
    }

    /**
     * Global search across searchable columns, plus the dropdown filters.
     *
     * @return array<string, mixed>
     */
    private function buildMatch(Request $request): array
    {
        $conditions = [];

        $search = trim((string) $request->input('search.value', $request->input('search', '')));

        if ($search !== '') {
            $regex = new Regex(preg_quote($search, '/'), 'i');
            $or    = [];

            foreach ($this->columns as $column) {
                if (($column['searchable'] ?? true) && isset($column['path'])) {
                    $or[] = [$column['path'] => $regex];
                }
            }

            if ($or !== []) {
                $conditions[] = ['$or' => $or];
            }
        }

        // Per-column search, which DataTables sends as columns[i][search][value].
        // The dues table puts a search box under each column and relies on it.
        foreach ((array) $request->input('columns', []) as $index => $sent) {
            $value = trim((string) ($sent['search']['value'] ?? ''));
            $path  = $this->columns[(int) $index]['path'] ?? null;

            if ($value !== '' && $path !== null && ($this->columns[(int) $index]['searchable'] ?? true)) {
                $conditions[] = [$path => new Regex(preg_quote($value, '/'), 'i')];
            }
        }

        foreach ($this->filters as $key => $spec) {
            $value = $request->input($key);

            if ($value === null || $value === '') {
                continue;
            }

            // A filter is either a path, or a path plus 'numeric' => true for
            // ones that compare against a stored number such as a status
            // code. The type is declared rather than guessed from the value,
            // because a unit legitimately named "123" must still match as a
            // string, while MongoDB would not match '6' against 6.
            $path    = is_array($spec) ? $spec['path'] : $spec;
            $numeric = is_array($spec) && ($spec['numeric'] ?? false);

            $conditions[] = [$path => $numeric ? (int) $value : $value];
        }

        return $conditions === [] ? [] : ['$and' => $conditions];
    }

    /**
     * @return array<string, int>
     */
    private function buildSort(Request $request): array
    {
        $index = $request->input('order.0.column');
        $dir   = $request->input('order.0.dir', 'asc') === 'desc' ? -1 : 1;

        if ($index !== null && isset($this->columns[(int) $index])) {
            $column = $this->columns[(int) $index];

            if (($column['orderable'] ?? true) && isset($column['path'])) {
                // _id is the tie-breaker so paging is stable when the sort
                // column has duplicates; without it MongoDB may return the
                // same document on two different pages.
                return [$column['path'] => $dir, '_id' => $dir];
            }
        }

        return ['_id' => -1];
    }

    private function collection(): \MongoDB\Collection
    {
        return DB::connection('mongodb')->getCollection($this->collection);
    }
}
