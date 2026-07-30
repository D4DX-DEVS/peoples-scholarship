<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Options for the filter dropdowns above the admin tables.
 *
 * Loading every row of the lookup table is both slow and useless: there are
 * 1481 units but only 389 of them appear on any application, and a select
 * with 1481 entries is not something anyone can use. Offering only the values
 * actually present also matches what the old client-side tables did, since
 * they built their dropdowns from the rows on the page.
 */
class FilterOptions
{
    /**
     * Distinct names of a related record, limited to those referenced by the
     * collection being filtered.
     *
     * @param  string $collection collection whose rows are being filtered
     * @param  string $localField foreign key on that collection
     * @param  class-string $model related model
     * @param  string $nameColumn column to show in the dropdown
     * @param  array  $baseMatch  the table's own base filter, so a scoped
     *                            listing only offers values it can show
     * @return list<string>
     */
    public static function forRelated(
        string $collection,
        string $localField,
        string $model,
        string $nameColumn,
        array $baseMatch = [],
    ): array {
        $ids = DB::connection('mongodb')
            ->getCollection($collection)
            ->distinct($localField, $baseMatch);

        $ids = array_values(array_filter($ids, fn ($id) => $id !== null && $id !== ''));

        if ($ids === []) {
            return [];
        }

        return $model::whereIn('id', $ids)
            ->orderBy($nameColumn)
            ->pluck($nameColumn)
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
