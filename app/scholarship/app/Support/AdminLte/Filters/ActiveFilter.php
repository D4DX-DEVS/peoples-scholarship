<?php

namespace App\Support\AdminLte\Filters;

use App\Support\AdminLte\ActiveChecker;
use App\Support\AdminLte\Builder;
use App\Support\AdminLte\FilterInterface;

/**
 * Flags the item matching the current page so the sidebar can highlight it.
 */
class ActiveFilter implements FilterInterface
{
    public function __construct(private ActiveChecker $activeChecker)
    {
    }

    public function transform(mixed $item, Builder $builder): mixed
    {
        if (! isset($item['header'])) {
            $item['active'] = $this->activeChecker->isActive($item);
        }

        return $item;
    }
}
