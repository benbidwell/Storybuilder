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

Route::get('/', 'HomeController@index');


Route::group(['middleware' => ['web'],'prefix'=>'admin','namespace'=>'Backend'], function () {
	Auth::routes();
	Route::get('/', function () {
	    return redirect('admin/login');
	});
});

Route::group(['prefix'=>'admin','namespace'=>'Backend','middleware' => 'auth','as'=>'admin.'],function(){
	
	//Auth::routes();
	Route::get('/', 'DashboardController@index');
	Route::resource('/admins', 'AdminController');
	Route::get('/profile', 'AdminProfileController@index');
	Route::post('/edit-profile', 'AdminProfileController@editProfile');
	Route::get('/change-password', 'AdminProfileController@change_password_form');
	Route::post('/change-password', 'AdminProfileController@changePassword');
	Route::resource('/events', 'EventsController');
	Route::resource('/stories', 'StoriesController');
	Route::resource('/users', 'UsersController');
	Route::post('/users/userauthorized', 'UsersController@userauthorized');
	Route::post('/users/userstatuschange', 'UsersController@userStatusChange');
	Route::resource('/notifications', 'NotificationController');
	Route::resource('/emailtemplates', 'EmailController');
	Route::get('/settings', 'SettingController@adminSettings');
	Route::post('/settings', 'SettingController@storeSettings');
	Route::post('/settings/edit/{id}', 'SettingController@updateSettings');

	Route::get('/pageviews','GoogleAnalyticsController@getVisitorsAndPageViews');
	Route::get('/fetchTopBrowsers','GoogleAnalyticsController@fetchTopBrowsers');
	Route::get('/views', 'GoogleAnalyticsController@pageViews');

});
Route::get('/ajax', 'AjaxController@index');
Route::post('/ajax', 'AjaxController@postindex');


// If routes not found then check on angular side
Route::any('{catchall}', function() {
   return view('master');
})->where('catchall', '.*');