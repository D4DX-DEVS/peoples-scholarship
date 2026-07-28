<?php

namespace App\Support\AdminLte;

interface FilterInterface
{
    /**
     * Transform a single menu item. Returning false removes the item.
     */
    public function transform(mixed $item, Builder $builder): mixed;
}
