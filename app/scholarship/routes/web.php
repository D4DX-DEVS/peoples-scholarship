<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ApplicationController;
use App\Http\Controllers\Admin\GrantedController;
use App\Http\Controllers\Admin\MeetingController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Controllers are referenced by class rather than by "Controller@method"
| string, which the framework stopped resolving in Laravel 9.
|
*/

Route::get('/', [HomeController::class, 'index'])->name('welcome');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
|
| Previously provided by Auth::routes(). That helper now lives in the
| laravel/ui package and registers registration, password-reset and email
| verification endpoints this application never implemented — the matching
| controllers were absent, so /register and /password/reset returned a 500.
| Only the routes actually in use are registered here.
|
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Retained so existing bookmarks and the public site's "Login" link keep working.
    Route::get('/auth/login', [LoginController::class, 'showLoginForm'])->name('getLogin');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Public application submission
|--------------------------------------------------------------------------
*/
Route::get('/application', [HomeController::class, 'addApplication'])->name('application-add');
Route::post('/application', [HomeController::class, 'checkNewUser'])->name('post-application-start');
Route::get('/application/start', [HomeController::class, 'getPersonApplication'])->name('get-application-final');
Route::post('/application/finish', [HomeController::class, 'savePersonApplication'])->name('post-application-final');
Route::get('/application/print', [HomeController::class, 'getApplicationPrint'])->name('get-application-print');

// Serves an uploaded document. The filename is constrained to a single path
// segment so it cannot escape the uploads directory (see FileController).
Route::get('/storage/uploads/{filename}', [FileController::class, 'show'])
    ->where('filename', '[A-Za-z0-9._-]+')
    ->name('getFile');

/*
|--------------------------------------------------------------------------
| Ajax lookups used by the application forms
|--------------------------------------------------------------------------
*/
Route::post('/ajax/area', [AjaxController::class, 'postArea'])->name('post-ajax-area');
Route::post('/ajax/unit', [AjaxController::class, 'postUnit'])->name('post-ajax-unit');
Route::post('/ajax/district', [AjaxController::class, 'postDistrict'])->name('post-ajax-district');
Route::post('/ajax/districtall', [AjaxController::class, 'postDistrictAll'])->name('post-ajax-district-all');
Route::post('/ajax/Category', [AjaxController::class, 'getCategory'])->name('get-ajax-category');

// Both of these previously carried the name "post-ajax-category"; the later
// registration silently won, so route('post-ajax-category') has always pointed
// at /ajax/category/course. That mapping is preserved and the shadowed route
// keeps its URL under a distinct name. Duplicate names are no longer merely
// redundant — they make route:cache fail outright on modern Laravel.
Route::post('/ajax/category', [AjaxController::class, 'postCategory'])->name('post-ajax-category-lookup');
Route::post('/ajax/category/course', [AjaxController::class, 'postCategory'])->name('post-ajax-category');

/*
|--------------------------------------------------------------------------
| Authenticated admin area
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('admin')->group(function () {

    Route::get('/', [AdminController::class, 'index'])->name('admin-dashboard');
    Route::get('/changepassword', [AdminController::class, 'viewChangePassword']);
    Route::post('/changepassword', [AdminController::class, 'changePassword'])->name('changePassword');

    Route::get('/area', [AdminController::class, 'getAreaResults'])->name('admin-area');
    Route::get('/area/data', [AdminController::class, 'areaData'])->name('area-data');
    Route::get('/area/export', [AdminController::class, 'areaExport'])->name('area-export');
    Route::post('/area', [AdminController::class, 'addArea'])->name('admin-post-add-area');
    Route::get('/area/admin/{areaid}/{districtid}', [AdminController::class, 'getAreaAdmin'])->name('admin_area_admin');
    Route::post('/area/admin', [AdminController::class, 'addAreaAdmin'])->name('admin-post-add-area-admin');
    Route::get('/area/delete/{id}', [AdminController::class, 'removeArea'])->name('admin_area_delete');

    Route::get('/unit', [AdminController::class, 'getUnitResults'])->name('admin-unit');
    Route::get('/unit/data', [AdminController::class, 'unitData'])->name('unit-data');
    Route::get('/unit/export', [AdminController::class, 'unitExport'])->name('unit-export');
    Route::post('/unit', [AdminController::class, 'addUnit'])->name('admin-post-add-unit');
    Route::get('/unit/edit/{id}', [AdminController::class, 'getEditUnit'])->name('admin-edit-unit');
    Route::post('/unit/edit', [AdminController::class, 'postEditUnit'])->name('admin-post-edit-unit');
    Route::get('/unit/delete/{id}', [AdminController::class, 'removeUnit'])->name('admin_unit_delete');

    Route::get('/statistics', [ApplicationController::class, 'getStatistics'])->name('get-statistics');
    Route::post('/statistics', [ApplicationController::class, 'postStatistics'])->name('post-statistics');

    // Note: a POST /admin/ajax/Category route was registered here pointing at
    // AjaxController@getAdminCategory, a method that does not exist. Nothing
    // referenced it — the admin course dropdown uses the top-level
    // /ajax/Category route — so it has been dropped rather than left to 500.

    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingsController::class, 'getSettings'])->name('admin-settings');
        Route::post('/category/new', [SettingsController::class, 'addNewCategory'])->name('add-new-category');
        Route::get('/category/delete/{id}', [SettingsController::class, 'removeCategory'])->name('admin_category_delete');
        Route::post('/course/new', [SettingsController::class, 'addNewCourse'])->name('add-new-course');
        Route::get('/course/admin/{courseid}', [SettingsController::class, 'getCourseAdmin'])->name('settings_course_admin');
        Route::post('/course/admin', [SettingsController::class, 'addCourseAdmin'])->name('settings-post-course-admin');
        Route::get('/course/delete/{id}', [SettingsController::class, 'removeCourse'])->name('admin_course_delete');
        Route::get('/years', [SettingsController::class, 'getYears'])->name('admin-year-settings');
        Route::post('/years', [SettingsController::class, 'addEditYears'])->name('admin-post-year-settings');
        Route::get('/years/delete/{id}', [SettingsController::class, 'removeYear'])->name('admin_year_delete');
    });

    Route::prefix('applications')->group(function () {
        Route::post('/search', [ApplicationController::class, 'applicationSearch'])->name('application-search');
        Route::get('/', [ApplicationController::class, 'getListing'])->name('get-applications-index');
        Route::get('/edit/{appli_id}/{pers_id}', [ApplicationController::class, 'getAppEdit'])->name('admin-app-edit');
        Route::post('/edit', [ApplicationController::class, 'postEditApplication'])->name('admin-post-edit-app');
        Route::get('/delete/{appli_id}/{pers_id}', [ApplicationController::class, 'deleteApp'])->name('admin-app-delete');
        Route::post('/approve', [ApplicationController::class, 'approveApplication'])->name('admin-approve-app');
        Route::get('/status/{appli_id}/{status}', [ApplicationController::class, 'setAppStatus'])->name('admin-set-app-status');
        Route::post('/grant', [ApplicationController::class, 'postGrantApp'])->name('admin-grant-app');
        Route::post('/reject', [ApplicationController::class, 'postRejectApp'])->name('admin-reject-app');
        Route::get('/application/view/{id}', [ApplicationController::class, 'viewApplication'])->name('view-application');
        Route::get('/application/print/{id}', [ApplicationController::class, 'printApplication'])->name('print-application');
        Route::post('/application/{id}', [ApplicationController::class, 'changeApplicationStatus'])->name('change-application-status');
        Route::get('/cancel-grant/{id}', [ApplicationController::class, 'cancelGrant'])->name('cancel-grant');
        Route::get('/application/meeting-sheet/{id}', [ApplicationController::class, 'getMeetingSheet'])->name('meeting-sheet');

        // The table fetches its rows from here, ten at a time.
        Route::get('/data', [ApplicationController::class, 'listingData'])->name('applications-data');
        Route::get('/export', [ApplicationController::class, 'exportListing'])->name('applications-export');

        // Declared last so the more specific routes above win. Keeps the
        // "get-applications-listing" name pointing here, as it always has.
        Route::get('/{status}', [ApplicationController::class, 'getListing'])->name('get-applications-listing');
    });

    Route::prefix('meetings')->group(function () {
        Route::get('/', [MeetingController::class, 'getMeetings'])->name('get-meetings');
        Route::post('/create', [MeetingController::class, 'createMeeting'])->name('add-meeting');
        Route::get('/view/{id}', [MeetingController::class, 'viewMeeting'])->name('view-meeting');
        Route::get('/meeting/edit/{id}', [MeetingController::class, 'editMeeting'])->name('edit-meeting');
        Route::post('/edit/{id}', [MeetingController::class, 'postEditMeeting'])->name('post-edit-meeting');
        Route::get('/delete/{id}', [MeetingController::class, 'deleteMeeting'])->name('delete-meeting');
        Route::get('/meeting/applications/{id}', [MeetingController::class, 'getApplications'])->name('meeting-applications');
        Route::get('/remove-application/{meeting_id}/{appli_id}', [MeetingController::class, 'removeApplication'])->name('remove-application');
        Route::post('/move-applications/{meeting_id}', [MeetingController::class, 'moveApplicationsMeeting'])->name('move-applications-meeting');
        Route::get('/add-applications/{meeting_id}', [MeetingController::class, 'getApplicationsList'])->name('get-meeting-applications');
        Route::post('/post-applications/{meeting_id}', [MeetingController::class, 'addApplications'])->name('post-add-applications');
    });

    Route::prefix('granted')->group(function () {
        Route::get('/', [GrantedController::class, 'getApplicationsGranted'])->name('get-files-granted');
        Route::get('/data', [GrantedController::class, 'grantedData'])->name('granted-data');
        Route::get('/export', [GrantedController::class, 'grantedExport'])->name('granted-export');
        Route::get('/installments-due', [GrantedController::class, 'getDuesThisMonth'])->name('get-installments-due-this-month');
        Route::get('/installments-due/data', [GrantedController::class, 'duesData'])->name('dues-data');
        Route::get('/installments-due/export', [GrantedController::class, 'duesExport'])->name('dues-export');
        Route::get('/installments/{id}', [GrantedController::class, 'getInstallments'])->name('edit-installments');
        Route::post('/installments/{id}', [GrantedController::class, 'postInstallments'])->name('save-installments');
        Route::post('/add/installment/{id}', [GrantedController::class, 'addInstallment'])->name('add-new-installment');
        Route::get('/delete/installment/{id}', [GrantedController::class, 'deleteInstallment'])->name('delete-installment');
    });
});
