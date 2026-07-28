<?php

namespace App\Support\AdminLte;

use App\Events\BuildingMenu;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * Supplies the admin sidebar menu to the published AdminLTE Blade views.
 *
 * The views call $adminlte->menu(); this class keeps that contract while the
 * unmaintained jeroennoten/laravel-adminlte package is no longer installed.
 */
class AdminLte
{
    private ?array $menu = null;

    public function __construct(
        private array $filters,
        private Dispatcher $events,
        private Container $container,
        private Repository $config,
    ) {
    }

    /**
     * @return array<int, mixed>
     */
    public function menu(): array
    {
        return $this->menu ??= $this->buildMenu();
    }

    /**
     * @return array<int, mixed>
     */
    protected function buildMenu(): array
    {
        $builder = new Builder($this->buildFilters());

        // Items declared in config/adminlte.php come first, then anything a
        // listener appends. This ordering matches the rendered sidebar.
        $builder->add(...$this->config->get('adminlte.menu', []));

        $this->events->dispatch(new BuildingMenu($builder));

        return $builder->menu;
    }

    /**
     * @return array<int, FilterInterface>
     */
    protected function buildFilters(): array
    {
        return array_map(fn (string $filter) => $this->container->make($filter), $this->filters);
    }
}
