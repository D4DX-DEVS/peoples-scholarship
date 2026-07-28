<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@index')->name('welcome');
Route::get('/auth/register', array('uses' => 'Auth\AuthController@getRegister', 'as' => 'getRegister'));
Route::get('/auth/login',  array('uses' => 'Auth\AuthController@getLogin', 'as' => 'getLogin'));

Route::get('/application', array('uses' => 'HomeController@addApplication', 'as' => 'application-add'));
Route::post('/application', array('uses' => 'HomeController@checkNewUser', 'as' => 'post-application-start'));
Route::get('/application/start', array('uses' => 'HomeController@getPersonApplication', 'as' => 'get-application-final'));
Route::post('/application/finish', array('uses' => 'HomeController@savePersonApplication', 'as' => 'post-application-final'));
Route::get('/application/print', array('uses' => 'HomeController@getApplicationPrint', 'as' => 'get-application-print'));

Route::get('/storage/uploads/{filename}', function ($filename)
{
    $path = storage_path('uploads'.DIRECTORY_SEPARATOR.$filename);
    return response()->file($path);

})->name('getFile');

// Everyone Ajax routes
Route::post('/ajax/category', array('uses' => 'AjaxController@postCategory', 'as' => 'post-ajax-category')); 

Route::post('/ajax/area', array('uses' => 'AjaxController@postArea', 'as' => 'post-ajax-area')); 
Route::post('/ajax/unit', array('uses' => 'AjaxController@postUnit', 'as' => 'post-ajax-unit')); 
Route::post('/ajax/category/course', array('uses' => 'AjaxController@postCategory', 'as' => 'post-ajax-category'));
Route::post('/ajax/district', array('uses' => 'AjaxController@postDistrict', 'as' => 'post-ajax-district'));
Route::post('/ajax/districtall', array('uses' => 'AjaxController@postDistrictAll', 'as' => 'post-ajax-district-all')); 
//Route::post('/ajax/areaid', array('uses' => 'AjaxController@postAreaId', 'as' => 'post-ajax-areaid')); 
Route::post('/ajax/Category',array('uses' => 'AjaxController@getCategory','as' => 'get-ajax-category'));

Auth::routes();

// Only Authorized Person Can Enter
Route::group(array('middleware' => 'auth'),function()
{	
	// An Authenticated User Can Logout
	Route::get('/auth/logout', array('uses' => 'Auth\AuthController@getLogout', 'as' => 'getLogout'));

	// Admin Routes
	Route::group(array('prefix' => '/admin'), function(){
		Route::get('/', array('uses' => 'Admin\AdminController@index', 'as' => 'admin-dashboard'));
		Route::get('/changepassword','Admin\AdminController@viewChangePassword');
		Route::post('/changepassword','Admin\AdminController@changePassword')->name('changePassword');		
		Route::get('/area', array('uses'=> 'Admin\AdminController@getAreaResults','as'=>'admin-area'));
		Route::get('/area/admin/{areaid}/{districtid}', array('uses'=> 'Admin\AdminController@getAreaAdmin','as'=>'admin_area_admin'));
		Route::get('/unit', array('uses'=> 'Admin\AdminController@getUnitResults','as'=>'admin-unit'));
		Route::get('/unit/edit/{id}', array('uses'=> 'Admin\AdminController@getEditUnit','as'=>'admin-edit-unit'));
		Route::get('/unit/delete/{id}', array('uses'=> 'Admin\AdminController@removeUnit','as'=>'admin_unit_delete'));
		Route::get('/area/delete/{id}', array('uses'=> 'Admin\AdminController@removeArea','as'=>'admin_area_delete'));
		Route::post('/area', array('uses'=> 'Admin\AdminController@addArea', 'as'=> 'admin-post-add-area'));
		Route::post('/unit', array('uses'=> 'Admin\AdminController@addUnit', 'as'=> 'admin-post-add-unit'));
		Route::post('/unit/edit', array('uses'=> 'Admin\AdminController@postEditUnit', 'as'=> 'admin-post-edit-unit'));
		Route::post('/area/admin', array('uses'=> 'Admin\AdminController@addAreaAdmin', 'as'=> 'admin-post-add-area-admin'));
		Route::get('/statistics',array('uses'=>'Admin\ApplicationController@getStatistics','as'=>'get-statistics'));
		Route::post('/statistics',array('uses'=>'Admin\ApplicationController@postStatistics','as'=>'post-statistics'));

		Route::post('/ajax/Category',array('uses' => 'AjaxController@getAdminCategory','as' => 'get-ajax-admin-category'));

		Route::group(array('prefix' => '/settings'),function()
    	{	
				Route::get('/',array('uses'=>'Admin\SettingsController@getSettings','as'=>'admin-settings'));
				Route::post('/category/new',array('uses'=>'Admin\SettingsController@addNewCategory','as'=>'add-new-category'));
				Route::post('/course/new',array('uses'=>'Admin\SettingsController@addNewCourse','as'=>'add-new-course'));
				Route::get('/course/admin/{courseid}', array('uses'=> 'Admin\SettingsController@getCourseAdmin','as'=>'settings_course_admin'));
				Route::post('/course/admin', array('uses'=> 'Admin\SettingsController@addCourseAdmin', 'as'=> 'settings-post-course-admin'));
				Route::get('/course/delete/{id}', array('uses'=> 'Admin\SettingsController@removeCourse','as'=>'admin_course_delete'));
				Route::get('/category/delete/{id}', array('uses'=> 'Admin\SettingsController@removeCategory','as'=>'admin_category_delete'));
				Route::get('/years', array('uses'=> 'Admin\SettingsController@getYears','as'=>'admin-year-settings'));
				Route::post('/years', array('uses'=> 'Admin\SettingsController@addEditYears', 'as'=> 'admin-post-year-settings'));
				Route::get('/years/delete/{id}', array('uses'=> 'Admin\SettingsController@removeYear','as'=>'admin_year_delete'));
						
		}); /*end /settings*/	
		Route::group(array('prefix' => '/applications'),function()
    	{
			Route::post('/search',array('uses'=>'Admin\ApplicationController@applicationSearch','as'=>'application-search'));
			Route::get('/',array('uses'=>'Admin\ApplicationController@getListing','as'=>'get-applications-listing'));
			Route::get('/{status}', array('uses' => 'Admin\ApplicationController@getListing', 'as' => 'get-applications-listing'));
			Route::get('/edit/{appli_id}/{pers_id}', array('uses' => 'Admin\ApplicationController@getAppEdit', 'as' => 'admin-app-edit'));
			Route::get('/delete/{appli_id}/{pers_id}', array('uses' => 'Admin\ApplicationController@deleteApp', 'as' => 'admin-app-delete'));
			Route::post('/edit', array('uses' => 'Admin\ApplicationController@postEditApplication', 'as' => 'admin-post-edit-app'));
			Route::post('/approve', array('uses' => 'Admin\ApplicationController@approveApplication', 'as' => 'admin-approve-app'));
			Route::get('/status/{appli_id}/{status}', array('uses' => 'Admin\ApplicationController@setAppStatus', 'as' => 'admin-set-app-status'));
			Route::post('/grant', array('uses' => 'Admin\ApplicationController@postGrantApp', 'as' => 'admin-grant-app'));
			Route::post('/reject', array('uses' => 'Admin\ApplicationController@postRejectApp', 'as' => 'admin-reject-app'));
			Route::get('/application/view/{id}',array('uses'=>'Admin\ApplicationController@viewApplication','as'=>'view-application'));
			Route::get('/application/print/{id}',array('uses'=>'Admin\ApplicationController@printApplication','as'=>'print-application'));
			Route::post('/application/{id}',array('uses'=>'Admin\ApplicationController@changeApplicationStatus','as'=>'change-application-status'));
			Route::get('/cancel-grant/{id}',array('uses'=>'Admin\ApplicationController@cancelGrant','as'=>'cancel-grant'));
			Route::get('/application/meeting-sheet/{id}',array('uses'=>'Admin\ApplicationController@getMeetingSheet','as'=>'meeting-sheet'));
		}); /*end /applications*/	
		Route::group(array('prefix' => '/meetings'),function()
    	{
			Route::get('/',array('uses'=>'Admin\MeetingController@getMeetings','as'=>'get-meetings'));
			Route::post('/create',array('uses'=>'Admin\MeetingController@createMeeting','as'=>'add-meeting'));
			Route::get('/meeting/edit/{id}',array('uses'=>'Admin\MeetingController@editMeeting','as'=>'edit-meeting'));
			Route::get('/view/{id}',array('uses'=>'Admin\MeetingController@viewMeeting','as'=>'view-meeting'));
			Route::post('/edit/{id}',array('uses'=>'Admin\MeetingController@postEditMeeting','as'=>'post-edit-meeting'));
			Route::get('/delete/{id}',array('uses'=>'Admin\MeetingController@deleteMeeting','as'=>'delete-meeting'));
			Route::get('/meeting/applications/{id}',array('uses'=>'Admin\MeetingController@getApplications','as'=>'meeting-applications'));
			Route::get('/remove-application/{meeting_id}/{appli_id}',array('uses'=>'Admin\MeetingController@removeApplication','as'=>'remove-application'));
			Route::post('/move-applications/{meeting_id}',array('uses'=>'Admin\MeetingController@moveApplicationsMeeting','as'=>'move-applications-meeting'));
			Route::get('/add-applications/{meeting_id}',array('uses'=>'Admin\MeetingController@getApplicationsList','as'=>'get-meeting-applications'));
			Route::post('/post-applications/{meeting_id}',array('uses'=>'Admin\MeetingController@addApplications','as'=>'post-add-applications'));
			}); /*end /meetings*/	
		Route::group(array('prefix' => '/granted'),function()
		{
			Route::get('/',array('uses'=>'Admin\GrantedController@getApplicationsGranted','as'=>'get-files-granted'));
			Route::get('/installments/{id}',array('uses'=>'Admin\GrantedController@getInstallments','as'=>'edit-installments'));
			Route::post('/add/installment/{id}',array('uses'=>'Admin\GrantedController@addInstallment','as'=>'add-new-installment'));
			Route::post('/installments/{id}',array('uses'=>'Admin\GrantedController@postInstallments','as'=>'save-installments'));
			Route::get('/delete/installment/{id}',array('uses'=>'Admin\GrantedController@deleteInstallment','as'=>'delete-installment'));
			Route::get('/installments-due',array('uses'=>'Admin\GrantedController@getDuesThisMonth','as'=>'get-installments-due-this-month'));
		}); /*end /granted*/	
    }); /*/adminend*/
}); /*/auth end*/