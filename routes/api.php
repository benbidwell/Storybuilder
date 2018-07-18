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


//Route::resource('stories','Frontend\StoriesController');
Route::get('get_countries', 'Frontend\APIController@getCountries');

//Route::group(['middleware' => 'api'], function () {
Route::post('register', 'Frontend\APIController@register');
Route::post('check_user', 'Frontend\APIController@checkUser');
Route::post('forgot_password', 'Frontend\APIController@forgotPassword');
Route::post('reset_password', 'Frontend\APIController@resetPassword');
Route::post('pundit_user_audio_on_video', 'Frontend\StoryPunditVideosController@addAudioToVideoUserNotLoggedIn');
Route::post('pundit_activate_account', 'Frontend\StoryPunditVideosController@punditActivateAccount');

Route::post('login', 'Frontend\Auth\LoginController@login');

Route::group(['middleware' => 'auth:api'], function () {
// User api
	Route::post('change_password', 'Frontend\APIController@changePassword');
	Route::get('edit_profile', 'Frontend\UsersController@editUserProfile');
	Route::post('update_profile', 'Frontend\UsersController@updateUserProfile');
	Route::post('update_profile_picture', 'Frontend\UsersController@updateUserProfilePicture');

// Events
	Route::get('events/search/{search?}','Frontend\EventsController@serachEvent');
	Route::get('events/limit/{limit?}/offset/{offset?}','Frontend\EventsController@allevents');
	Route::get('events/autosearch','Frontend\EventsController@AutoloadSearching');
	Route::resource('events','Frontend\EventsController');
	Route::post('events/update_event_picture/{id}', 'Frontend\EventsController@updateEventPicture');
	Route::get('events/confirmationmessage/delete','Frontend\EventsController@delete_event_confirmation');

// Story
	Route::get('stories/{event_id}/allstories','Frontend\StoriesController@allstories');
	Route::post('stories2/{id}','Frontend\StoriesController@updatemystory');
	Route::get('stories/search/{search?}','Frontend\StoriesController@serachStories');
	Route::get('event/{event_id}/stories/search/{search?}','Frontend\StoriesController@serachStories');
	Route::get('stories/searchstories', 'Frontend\StoriesController@AutoCompleteSearching');
	Route::resource('stories','Frontend\StoriesController');
	Route::get('get_story_details/{story_id}', 'Frontend\StoriesController@getStoryDetails');
	Route::get('stories/confirmationmessage/delete','Frontend\StoriesController@delete_story_confirmation');
	Route::get('stories/confirmationmessage/saverecording','Frontend\StoriesController@save_recording_confirmation');
	
// Story Media
	Route::post('story_media', 'Frontend\StoryMediasController@saveMedia');
	Route::post('story_media_upload', 'Frontend\StoryMediasController@saveMediaUploaded');
	Route::post('story_gmedia_upload', 'Frontend\StoryMediasController@savegMediaUploaded');
	Route::get('story_media/{story_id}/limit/{limit?}/offset/{offset?}','Frontend\StoryMediasController@allmedia');
	Route::post('story_media_google_plus', 'Frontend\StoryMediasController@saveMediaGooglePlus');
	Route::post('stories/removestorypicture/{id}', 'Frontend\StoriesController@removeStoryPicture');
// Story Effects
	Route::post('add_picture_effect', 'Frontend\StoryMediasController@addPictureEffect');
	Route::post('story_make_video', 'Frontend\StoriesController@makeVideo');
	Route::post('story_add_audio_on_video', 'Frontend\StoriesController@addAudioToVideo');
	Route::post('story_add_water_mark', 'Frontend\StoriesController@addWaterMark');
	Route::post('story_add_text_effect', 'Frontend\StoriesController@addTextEffectOnVideo');
// Story Audio
	Route::post('story_audio', 'Frontend\StoryAudiosController@saveAudio');
	Route::get('get_story_audio/{story_id}', 'Frontend\StoryAudiosController@getAllAudio');
	//Route::post('story_video_publish', 'Frontend\StoriesController@storyPublishVideo');
	Route::get('get_published_videos', 'Frontend\StoriesController@allPublishedVideos');

	// Pundit
	Route::post('pundit_add_audio_on_video', 'Frontend\StoryPunditVideosController@addAudioToVideo');
	Route::get('pundit_all_unpublished_videos', 'Frontend\StoryPunditVideosController@allPunditUnPublishedVideos');

	Route::get('pundit_all_published_videos', 'Frontend\StoryPunditVideosController@allPunditPublishedVideos');


	Route::post('post_curl_response', 'Frontend\APIController@postCurlResponse');
	Route::post('post_curl_response_fb', 'Frontend\APIController@postCurlResponseFB');
	Route::post('get_curl_response', 'Frontend\APIController@getCurlResponse');

	
	Route::post('get_curl_response_insta', 'Frontend\APIController@getCurlResponseInsta');
	Route::post('pundit_video_publish_logged_user', 'Frontend\StoryPunditVideosController@punditPublishVideoLoggedUser');

	Route::get('getAdminSettings','Frontend\APIController@getSettings');
});
Route::get('storyview/{story_id}/{pundit_id?}', 'Frontend\StoriesController@increment_view');
Route::get('storyshare/{story_id}/{pundit_id?}', 'Frontend\StoriesController@increment_share');
Route::post('getCurlResponseTwit', 'Frontend\APIController@getCurlResponseTwit');
Route::get('stories/{story}', 'Frontend\StoriesController@show');
Route::post('pundit_published_story/{story_id}', 'Frontend\StoryPunditVideosController@allPunditPublishedVideosByStory');
Route::post('popularVideo', 'Frontend\StoryPunditVideosController@popularVideo');
Route::get('pundit_published_pundit/{pundit_id}', 'Frontend\StoryPunditVideosController@allPunditPublishedVideosByPundit');

Route::post('twitterVideoUpload', 'Frontend\APIController@twitterVideoUpload');
Route::post('pundit_story_update', 'Frontend\StoryPunditVideosController@pundit_story_update');
Route::post('event_analysis', 'Frontend\EventsController@event_analysis');
Route::post('pundit_video_publish', 'Frontend\StoryPunditVideosController@punditPublishVideo');
Route::post('pundit_add_audio_on_video', 'Frontend\StoryPunditVideosController@addAudioToVideo');


Route::get('pundit_published_story/{story_id}/{pundit_story_id}', 'Frontend\StoryPunditVideosController@getPunditStory');
Route::post('twitterImport', 'Frontend\StoryMediasController@twitterImport');
Route::post('gettoken', 'Frontend\StoryMediasController@gettoken');
Route::post('twitterImport1', 'Frontend\StoryMediasController@twitterImport1');
Route::post('twitterKeyword', 'Frontend\StoryMediasController@twitterKeyword');
Route::post('twitterKeyword1', 'Frontend\StoryMediasController@twitterKeyword1');
Route::post('instagramToken', 'Frontend\StoryMediasController@instagramToken');

Route::post('googleKeywordSerach','Frontend\StoryMediasController@googleKeywordSerach');
Route::post('instaHashtagSerach','Frontend\StoryMediasController@instaHashtagSerach');
Route::post('facebookHashtagSerach','Frontend\StoryMediasController@facebookHashtagSerach');


