<?php

namespace App\Support\AdminLte;

/**
 * Collects sidebar menu items and runs each one through the configured filters.
 *
 * This replaces JeroenNoten\LaravelAdminLte\Menu\Builder. The package only
 * supported AdminLTE 2 on Laravel <= 5.x and is no longer maintained, but the
 * published Blade views under resources/views/vendor/adminlte still expect the
 * exact array shape it produced, so the behaviour here is kept identical.
 */
class Builder
{
    /**
     * @var array<int, mixed>
     */
    public array $menu = [];

    /**
     * @param  array<int, FilterInterface>  $filters
     */
    public function __construct(private array $filters = [])
    {
    }

    public function add(...$items): void
    {
        foreach ($this->transformItems($items) as $item) {
            $this->menu[] = $item;
        }
    }

    /**
     * @param  array<int, mixed>  $items
     * @return array<int, mixed>
     */
    public function transformItems(array $items): array
    {
        return array_filter(array_map([$this, 'applyFilters'], $items));
    }

    /**
     * A plain string is a sidebar header and is passed through untouched.
     */
    protected function applyFilters(mixed $item): mixed
    {
        if (is_string($item)) {
            return $item;
        }

        foreach ($this->filters as $filter) {
            $item = $filter->transform($item, $this);

            // A filter may reject an item outright (e.g. the gate check).
            if ($item === false) {
                return false;
            }
        }

        if (isset($item['header'])) {
            $item = $item['header'];
        }

        return $item;
    }
}
