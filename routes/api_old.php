<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('story_audio', 'Frontend\StoryAudiosController@saveAudio');
Route::get('get_story_audio/{story_id}', 'Frontend\StoryAudiosController@getAllAudio');

//Route::resource('stories','Frontend\StoriesController');
Route::get('get_countries', 'Frontend\APIController@getCountries');

//Route::group(['middleware' => 'api'], function () {
Route::post('register', 'Frontend\APIController@register');
Route::post('check_user', 'Frontend\APIController@checkUser');
Route::post('forgot_password', 'Frontend\APIController@forgotPassword'); 
Route::post('reset_password', 'Frontend\APIController@resetPassword'); 
Route::post('change_password', 'Frontend\APIController@changePassword');



Route::post('login', 'Frontend\Auth\LoginController@login'); 

 
Route::group(['middleware' => 'auth:api'], function () {
	Route::get('edit_profile', 'Frontend\UsersController@editUserProfile');
	Route::post('update_profile', 'Frontend\UsersController@updateUserProfile');
	Route::post('update_profile_picture', 'Frontend\UsersController@updateUserProfilePicture');
	Route::get('events/search/{search?}','Frontend\EventsController@serachEvent');
	Route::get('events/limit/{limit?}/offset/{offset?}','Frontend\EventsController@allevents');

	Route::post('story_media', 'Frontend\StoryMediasController@saveMedia');
	Route::post('story_media_upload', 'Frontend\StoryMediasController@saveMediaUploaded');
	Route::get('story_media/{story_id}/limit/{limit?}/offset/{offset?}','Frontend\StoryMediasController@allmedia');
	//Route::post('story_make_video', 'Frontend\StoriesController@makeVideo');
	//Route::post('story_add_audio_video', 'Frontend\StoriesController@addAudioToVideo');
//	Route::post('story_publish_video', 'Frontend\StoriesController@publishVideo');
	//Route::get('all_published_videos', 'Frontend\StoriesController@storyPublishedVideos');
	Route::post('story_media_google_plus', 'Frontend\StoryMediasController@saveMediaGooglePlus');
	//Route::post('story_media', 'Frontend\StoryMediasController@saveMedia');
	//Route::post('story_media_upload', 'Frontend\StoryMediasController@saveMediaUploaded');
	
	//Route::get('story_media/{story_id}/limit/{limit?}/offset/{offset?}','Frontend\StoryMediasController@allmedia');

	Route::resource('events','Frontend\EventsController');

	Route::get('stories/allstories','Frontend\StoriesController@allstories');
	Route::get('stories/search/{search?}','Frontend\StoriesController@serachStories');

	Route::resource('stories','Frontend\StoriesController');
});  




Route::post('twitterImport', 'Frontend\StoryMediasController@twitterImport');
Route::post('gettoken', 'Frontend\StoryMediasController@gettoken');
Route::post('twitterImport1', 'Frontend\StoryMediasController@twitterImport1');
Route::post('twitterKeyword', 'Frontend\StoryMediasController@twitterKeyword');