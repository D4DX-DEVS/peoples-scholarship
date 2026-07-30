<?php

namespace App\Providers;

use App\Application;
use App\Events\BuildingMenu;
use App\Person;
use App\Support\Html\FormBuilder;
use App\Support\Html\FormFacade;
use App\Yearsetting;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(Dispatcher $events): void
    {
        Schema::defaultStringLength(191);

        // The UI is AdminLTE 2 / Bootstrap 3. Laravel's pagination default
        // switched to Tailwind markup in 8.x, which renders unstyled here.
        Paginator::useBootstrapThree();

        $this->registerAdminMenu($events);
        $this->registerValidators();
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Backs the "Form" alias the Blade views use. Replaces the abandoned
        // laravelcollective/html package; see App\Support\Html\FormBuilder.
        $this->app->singleton(FormBuilder::class, function ($app) {
            return new FormBuilder($app['request'], $app['session.store']);
        });

        AliasLoader::getInstance()->alias('Form', FormFacade::class);
    }

    /**
     * Build the admin sidebar, including the per-status application counts.
     *
     * The counts are resolved lazily inside the listener rather than in boot():
     * querying at boot meant every artisan command (migrate, key:generate,
     * config:cache) failed whenever the database was unreachable or not yet
     * seeded, which broke first-time deploys.
     */
    private function registerAdminMenu(Dispatcher $events): void
    {
        $events->listen(BuildingMenu::class, function (BuildingMenu $event): void {
            $yearid = optional(Yearsetting::orderBy('id', 'desc')->first())->id;

            $countByStatus = static fn (int $status): int => Application::where('year_id', $yearid)
                ->where('status', $status)
                ->count();

            $event->menu->add([
                'text' => 'Applications',
                'url' => 'admin/applications',
                'icon' => 'file',
                'label' => Application::where('year_id', $yearid)->count(),
                'label_color' => 'success',
            ]);

            $event->menu->add([
                'text' => 'Application Status',
                'icon' => 'wpforms ',
                'submenu' => [
                    [
                        'text' => 'Waiting',
                        'url' => 'admin/applications/registered',
                        'icon' => 'hourglass-start',
                        'label' => $countByStatus(1),
                        'label_color' => 'info',
                    ],
                    [
                        'text' => 'Verified',
                        'url' => 'admin/applications/verified',
                        'icon' => 'check',
                        'label' => $countByStatus(2),
                        'label_color' => 'primary',
                    ],
                    [
                        'text' => 'For Interview',
                        'url' => 'admin/applications/interview',
                        'icon' => 'user',
                        'label' => $countByStatus(3),
                        'label_color' => 'warning',
                    ],
                    [
                        'text' => 'On Meeting',
                        'url' => 'admin/applications/meeting',
                        'icon' => 'calendar-o',
                        'label' => $countByStatus(4),
                        'label_color' => 'default',
                    ],
                    [
                        'text' => 'Granted',
                        'url' => 'admin/applications/granted',
                        'icon' => 'thumbs-up',
                        'label' => $countByStatus(6),
                        'label_color' => 'success',
                    ],
                    [
                        'text' => 'Pending',
                        'url' => 'admin/applications/pending',
                        'icon' => 'reply',
                        'label' => $countByStatus(7),
                        'label_color' => 'warning',
                    ],
                    [
                        'text' => 'Rejected',
                        'url' => 'admin/applications/rejected',
                        'icon' => 'trash-o',
                        'label' => $countByStatus(5),
                        'label_color' => 'danger',
                    ],
                    [
                        'text' => 'Incomplete',
                        'url' => 'admin/applications/incomplete',
                        'icon' => 'hourglass-end',
                        'label' => $countByStatus(9),
                        'label_color' => 'info',
                    ],
                    [
                        'text' => 'Cancelled',
                        'url' => 'admin/applications/cancelled',
                        'icon' => 'close',
                        'label' => $countByStatus(10),
                        'label_color' => 'danger',
                    ],
                    [
                        'text' => 'Completed',
                        'url' => 'admin/applications/completed',
                        'icon' => 'thumbs-up',
                        'label' => $countByStatus(8),
                        'label_color' => 'success',
                    ],
                ],
            ]);

            $event->menu->add([
                'text' => 'Meetings',
                'url' => 'admin/meetings',
                'icon' => 'users',
            ]);

            $event->menu->add([
                'text' => 'Granted',
                'icon' => 'money',
                'submenu' => [
                    [
                        'text' => 'Granded List',
                        'url' => 'admin/granted',
                        'icon' => 'list-alt',
                    ],
                    [
                        'text' => 'Installments Due',
                        'url' => 'admin/granted/installments-due',
                        'icon' => 'clock-o',
                    ],
                ],
            ]);

            $event->menu->add([
                'text' => 'Statistics',
                'url' => 'admin/statistics',
                'icon' => 'area-chart',
            ]);

            $event->menu->add('SETTINGS');

            $event->menu->add([
                'text' => 'Areas',
                'icon_color' => 'red',
                'url' => 'admin/area',
            ]);

            $event->menu->add([
                'text' => 'Units',
                'icon_color' => 'yellow',
                'url' => 'admin/unit',
            ]);

            $event->menu->add([
                'text' => 'Course Settings',
                'icon_color' => 'aqua',
                'url' => 'admin/settings',
            ]);

            $event->menu->add([
                'text' => 'Year Settings',
                'icon_color' => 'green',
                'url' => 'admin/settings/years',
            ]);

            $event->menu->add('ACCOUNT SETTINGS');

            $event->menu->add([
                'text' => 'Change Password',
                'url' => 'admin/changepassword',
                'icon' => 'lock',
            ]);
        });
    }

    /**
     * An applicant may only appear once per scholarship year.
     */
    private function registerValidators(): void
    {
        // MongoDB has no joins, so whereHas cannot be translated. The
        // applicants who already have an application in the year are
        // resolved first, then matched by key — same result, two queries.
        $applicantsInYear = fn ($yearId) => Application::where('year_id', '=', $yearId)->pluck('persid');

        Validator::extend('uniqueMobileAndYear', function ($attribute, $value, $parameters, $validator) use ($applicantsInYear) {
            return Person::whereIn('id', $applicantsInYear($parameters[0]))
                ->where('mobile', $value)->count() === 0;
        });

        Validator::extend('uniqueAadharAndYear', function ($attribute, $value, $parameters, $validator) use ($applicantsInYear) {
            return Person::whereIn('id', $applicantsInYear($parameters[0]))
                ->where('aadhar', $value)->count() === 0;
        });
    }
}
