<?php

namespace App\Events;

use App\Support\AdminLte\Builder;

/**
 * Dispatched while the admin sidebar is being assembled.
 *
 * Listeners call $event->menu->add(...) to append items. Replaces
 * JeroenNoten\LaravelAdminLte\Events\BuildingMenu.
 */
class BuildingMenu
{
    public function __construct(public Builder $menu)
    {
    }
}
