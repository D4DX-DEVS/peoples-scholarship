<?php

namespace App\Support\AdminLte;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Decides whether a sidebar item corresponds to the page currently being viewed.
 *
 * Port of JeroenNoten\LaravelAdminLte\Menu\ActiveChecker with identical matching
 * rules, so the highlighted sidebar entry stays exactly as before.
 */
class ActiveChecker
{
    public function __construct(
        private Request $request,
        private UrlGenerator $url,
    ) {
    }

    public function isActive(mixed $item): bool
    {
        if (isset($item['active'])) {
            return $this->isExplicitActive($item['active']);
        }

        if (isset($item['submenu'])) {
            return $this->containsActive($item['submenu']);
        }

        if (isset($item['href'])) {
            return $this->checkExactOrSub($item['href']);
        }

        // Supported for backwards compatibility with items declared using 'url'.
        if (isset($item['url'])) {
            return $this->checkExactOrSub($item['url']);
        }

        return false;
    }

    protected function checkExactOrSub(string $url): bool
    {
        return $this->checkExact($url) || $this->checkSub($url);
    }

    protected function checkExact(string $url): bool
    {
        return $this->checkPattern($url);
    }

    protected function checkSub(string $url): bool
    {
        return $this->checkPattern($url.'/*');
    }

    protected function checkPattern(string $pattern): bool
    {
        return Str::is($this->url->to($pattern), $this->request->fullUrl());
    }

    /**
     * @param  array<int, mixed>  $items
     */
    protected function containsActive(array $items): bool
    {
        foreach ($items as $item) {
            if ($this->isActive($item)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<int, string>  $active
     */
    private function isExplicitActive(array $active): bool
    {
        foreach ($active as $url) {
            if ($this->checkExact($url)) {
                return true;
            }
        }

        return false;
    }
}
