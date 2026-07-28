<?php

namespace App\Support\AdminLte\Filters;

use App\Support\AdminLte\Builder;
use App\Support\AdminLte\FilterInterface;

/**
 * Recursively filters nested submenu items and marks an open submenu.
 */
class SubmenuFilter implements FilterInterface
{
    public function transform(mixed $item, Builder $builder): mixed
    {
        if (isset($item['submenu'])) {
            $item['submenu'] = $builder->transformItems($item['submenu']);
            $item['submenu_open'] = $item['active'] ?? false;
            $item['submenu_classes'] = $this->makeSubmenuClasses();
            $item['submenu_class'] = implode(' ', $item['submenu_classes']);
        }

        return $item;
    }

    /**
     * @return array<int, string>
     */
    protected function makeSubmenuClasses(): array
    {
        return ['treeview-menu'];
    }
}
