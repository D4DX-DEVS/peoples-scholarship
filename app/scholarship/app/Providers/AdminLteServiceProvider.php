<?php

namespace App\Providers;

use App\Support\AdminLte\AdminLte;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

/**
 * Wires up the in-repo replacement for jeroennoten/laravel-adminlte.
 *
 * The package was abandoned at AdminLTE 2 / Laravel 5 and blocked the framework
 * upgrade. Its Blade views and translations were already published into this
 * repository, so only the "adminlte" view/translation namespaces and the
 * $adminlte view variable needed re-registering. The theme markup is unchanged.
 */
class AdminLteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AdminLte::class, function (Container $app) {
            return new AdminLte(
                $app['config']['adminlte.filters'],
                $app['events'],
                $app,
                $app['config'],
            );
        });
    }

    public function boot(): void
    {
        $this->loadViewsFrom(resource_path('views/vendor/adminlte'), 'adminlte');
        $this->loadTranslationsFrom(lang_path('vendor/adminlte'), 'adminlte');

        // Only adminlte::page renders the sidebar, matching the original package.
        View::composer('adminlte::page', function ($view) {
            $view->with('adminlte', $this->app->make(AdminLte::class));
        });
    }
}
