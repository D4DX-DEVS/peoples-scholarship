<?php

namespace App\Support\AdminLte\Filters;

use App\Support\AdminLte\Builder;
use App\Support\AdminLte\FilterInterface;
use Illuminate\Contracts\Routing\UrlGenerator;

/**
 * Resolves each item's 'url' or 'route' into a concrete 'href'.
 */
class HrefFilter implements FilterInterface
{
    public function __construct(private UrlGenerator $urlGenerator)
    {
    }

    public function transform(mixed $item, Builder $builder): mixed
    {
        if (! isset($item['header'])) {
            $item['href'] = $this->makeHref($item);
        }

        return $item;
    }

    protected function makeHref(array $item): string
    {
        if (isset($item['url'])) {
            return $this->urlGenerator->to($item['url']);
        }

        if (isset($item['route'])) {
            return $this->urlGenerator->route($item['route']);
        }

        return '#';
    }
}
