<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Events\Dispatcher;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use DB;
use App\Person;
use App\Application;
use App\Yearsetting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Dispatcher $events)
    {
        Schema::defaultStringLength(191);
        $yearid=Yearsetting::orderBy('id', 'desc')->first()->id;
        $events->listen(BuildingMenu::class, function (BuildingMenu $event) use ($yearid) {
             
            $event->menu->add([
                'text'        => 'Applications',
                'url'         => 'admin/applications',
                'icon'        => 'file',
                'label'       => Application::where('year_id',$yearid)->count(),
                'label_color' => 'success',
                ]);

            $event->menu->add([    
                'text'    => 'Application Status',
                'icon'    => 'wpforms ',
                'submenu' => [
                    [
                        'text' => 'Waiting',
                        'url'  => 'admin/applications/registered',
                        'icon' => 'hourglass-start', 
                        'label' => Application::where([['year_id', '=', $yearid], ['status', '=', 1]])->count(),
                        'label_color' => 'info',                   
                    ],
                    [
                        'text' => 'Verified',
                        'url'  => 'admin/applications/verified',
                        'icon' => 'check', 
                        'label'       => Application::where([['year_id', '=', $yearid], ['status', '=', 2]])->count(),
                        'label_color' => 'primary',
                    ],
                    [
                        'text' => 'For Interview',
                        'url'  => 'admin/applications/interview',
                        'icon' => 'user', 
                        'label'       => Application::where([['year_id', '=', $yearid], ['status', '=', 3]])->count(),
                        'label_color' => 'warning',
                    ],
                    [
                        'text' => 'On Meeting',
                        'url'  => 'admin/applications/meeting',
                        'icon' => 'calendar-o', 
                        'label'       => Application::where([['year_id', '=', $yearid], ['status', '=', 4]])->count(),
                        'label_color' => 'default',
                    ],
                    [
                        'text' => 'Granted',
                        'url'  => 'admin/applications/granted',
                        'icon' => 'thumbs-up', 
                        'label'       => Application::where([['year_id', '=', $yearid], ['status', '=', 6]])->count(),
                        'label_color' => 'success',
                    ],
                    [
                        'text' => 'Pending',
                        'url'  => 'admin/applications/pending',
                        'icon' => 'reply', 
                        'label'       => Application::where([['year_id', '=', $yearid], ['status', '=', 7]])->count(),
                        'label_color' => 'warning',
                    ],
                    [
                        'text' => 'Rejected',
                        'url'  => 'admin/applications/rejected',
                        'icon' => 'trash-o', 
                        'label'       => Application::where([['year_id', '=', $yearid], ['status', '=', 5]])->count(),
                        'label_color' => 'danger',
                    ],
                    [
                        'text' => 'Incomplete',
                        'url'  => 'admin/applications/incomplete',
                        'icon' => 'hourglass-end', 
                        'label'       => Application::where([['year_id', '=', $yearid], ['status', '=', 9]])->count(),
                        'label_color' => 'info',
                    ],
                    [
                        'text' => 'Cancelled',
                        'url'  => 'admin/applications/cancelled',
                        'icon' => 'close', 
                        'label'       => Application::where([['year_id', '=', $yearid], ['status', '=', 10]])->count(),
                        'label_color' => 'danger',
                    ],
                    [
                        'text' => 'Completed',
                        'url'  => 'admin/applications/completed',
                        'icon' => 'thumbs-up', 
                        'label'       => Application::where([['year_id', '=', $yearid], ['status', '=', 8]])->count(),
                        'label_color' => 'success',
                    ],
                ],
            ]);
            $event->menu->add([
                'text' => 'Meetings',
                'url'  => 'admin/meetings',
                'icon' => 'users',
            ]);
            $event->menu->add([
                'text' => 'Granted',            
                'icon' => 'money',
                'submenu' => [
                    [
                        'text' => 'Granded List',
                        'url'  => 'admin/granted',
                        'icon' => 'list-alt',                    
                    ],
                    [
                        'text' => 'Installments Due',
                        'url'  => 'admin/granted/installments-due',
                        'icon' => 'clock-o', 
                    ],
                ],  
            ]);
            $event->menu->add([
                'text' => 'Statistics',
                'url'  => 'admin/statistics',
                'icon' => 'area-chart',
            ]);
            $event->menu->add('SETTINGS');
            $event->menu->add([
                'text'       => 'Areas',
                'icon_color' => 'red',
                'url'  => 'admin/area',
            ]);
            $event->menu->add([
                'text'       => 'Units',
                'icon_color' => 'yellow',
                'url'  => 'admin/unit',
            ]);
            $event->menu->add([
                'text'       => 'Course Settings',
                'icon_color' => 'aqua',
                'url'  => 'admin/settings',
            ]);
            $event->menu->add([
                'text'       => 'Year Settings',
                'icon_color' => 'green',
                'url'  => 'admin/settings/years',
            ]);
            $event->menu->add('ACCOUNT SETTINGS');
            $event->menu->add([
                'text' => 'Change Password',
                'url'  => 'admin/changepassword',
                'icon' => 'lock',
            ]);
        });

        Validator::extend('uniqueMobileAndYear', function ($attribute, $value, $parameters, $validator) {
            $count = Person::with('application')
            ->whereHas('application', function ($query) use($parameters) {
                 $query->where('year_id', '=', $parameters[0]);
           })->where('mobile', $value )->count();      
            return $count === 0;
        });

        Validator::extend('uniqueAadharAndYear', function ($attribute, $value, $parameters, $validator) {
            $count = Person::with('application')
            ->whereHas('application', function ($query) use($parameters) {
                 $query->where('year_id', '=', $parameters[0]);
           })->where('aadhar', $value )->count();      
            return $count === 0;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
