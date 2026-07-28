<?php

namespace App\Support\AdminLte\Filters;

use App\Support\AdminLte\Builder;
use App\Support\AdminLte\FilterInterface;

/**
 * Builds the CSS class lists the sidebar and top-nav partials render.
 */
class ClassesFilter implements FilterInterface
{
    public function transform(mixed $item, Builder $builder): mixed
    {
        if (! isset($item['header'])) {
            $item['classes'] = $this->makeClasses($item);
            $item['class'] = implode(' ', $item['classes']);
            $item['top_nav_classes'] = $this->makeClasses($item, true);
            $item['top_nav_class'] = implode(' ', $item['top_nav_classes']);
        }

        return $item;
    }

    /**
     * @return array<int, string>
     */
    protected function makeClasses(array $item, bool $topNav = false): array
    {
        $classes = [];

        if (! empty($item['active'])) {
            $classes[] = 'active';
        }

        if (isset($item['submenu'])) {
            $classes[] = $topNav ? 'dropdown' : 'treeview';
        }

        return $classes;
    }
}
