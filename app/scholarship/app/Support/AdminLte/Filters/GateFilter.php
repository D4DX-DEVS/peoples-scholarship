<?php

namespace App\Support\AdminLte\Filters;

use App\Support\AdminLte\Builder;
use App\Support\AdminLte\FilterInterface;
use Illuminate\Contracts\Auth\Access\Gate;

/**
 * Hides menu items the current user is not authorised to see.
 *
 * Items opt in by declaring a 'can' key; everything else passes through.
 */
class GateFilter implements FilterInterface
{
    public function __construct(private Gate $gate)
    {
    }

    public function transform(mixed $item, Builder $builder): mixed
    {
        return $this->isVisible($item) ? $item : false;
    }

    protected function isVisible(array $item): bool
    {
        if (! isset($item['can'])) {
            return true;
        }

        if (isset($item['model'])) {
            return $this->gate->allows($item['can'], $item['model']);
        }

        return $this->gate->allows($item['can']);
    }
}
