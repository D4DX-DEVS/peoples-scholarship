<?php

namespace App\Support;

use MongoDB\Laravel\Eloquent\Builder as BaseBuilder;

use function collect;
use function ctype_digit;
use function func_num_args;
use function in_array;
use function is_iterable;
use function is_string;
use function ltrim;

/**
 * Eloquent builder that keeps integer columns integer.
 *
 * MySQL compared '1001' to an integer column by coercing the string.
 * MongoDB does not: a document with _id 1001 is simply not matched by
 * the string '1001'. Route parameters and request input always arrive
 * as strings, so without this every lookup by id — every view, edit
 * and delete page — would silently find nothing.
 *
 * Which columns are integers cannot be guessed from their names: the
 * persons collection stores area, district and unit as integers, and
 * areaauths uses areaid/districtid. Each model therefore declares its
 * own integer columns, taken from the original MySQL schema, and only
 * those are cast — casting something like mobile, which is stored as a
 * string, would break it in exactly the same way.
 */
class MongoBuilder extends BaseBuilder
{
    /** @inheritdoc */
    public function find($id, $columns = ['*'])
    {
        return parent::find($this->castFor($this->model->getKeyName(), $id), $columns);
    }

    /** @inheritdoc */
    public function findMany($ids, $columns = ['*'])
    {
        $key = $this->model->getKeyName();

        return parent::findMany(
            collect($ids)->map(fn ($id) => $this->castFor($key, $id))->all(),
            $columns,
        );
    }

    /** @inheritdoc */
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        if (is_string($column)) {
            // where($column, $value) puts the value in $operator.
            if (func_num_args() === 2) {
                $operator = $this->castFor($column, $operator);
            } elseif (func_num_args() >= 3) {
                $value = $this->castFor($column, $value);
            }
        }

        return parent::where($column, $operator, $value, $boolean);
    }

    /** @inheritdoc */
    public function whereIn($column, $values, $boolean = 'and', $not = false)
    {
        if (is_string($column) && is_iterable($values)) {
            $values = collect($values)->map(fn ($value) => $this->castFor($column, $value))->all();
        }

        return parent::whereIn($column, $values, $boolean, $not);
    }

    /**
     * Cast a numeric string to int, but only for a declared integer column.
     */
    private function castFor(string $column, mixed $value): mixed
    {
        if (! is_string($value) || $value === '') {
            return $value;
        }

        if (! ctype_digit(ltrim($value, '-'))) {
            return $value;
        }

        return in_array($column, $this->model->getIntegerColumns(), true)
            ? (int) $value
            : $value;
    }
}
