<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\FrontendController;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\User;
use Validator;
use App\Models\AdminSetting;

class UsersController extends FrontendController
{
    //
    function editUserProfile(Request $request){
    	try {
    	    if (! $user = JWTAuth::parseToken()->authenticate()) {
            	//return response()->json(['user_not_found'], 404);
            	return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
        	}else{
                $setting=AdminSetting::first();
                if($setting->is_authenticate){
                    $check=1;
                } else {
                    $check=$user->authorized;
                }
        		$data = array(
        			'first_name'		=> $user->first_name,
        			'last_name'	 		=> $user->last_name,
        			'email'				=> $user->email,
        			'country_id'		=> $user->country_id,
					'profile_picture'	=> $user->profile_picture,
					'status'			=> $user->status,
					'authorized'		=> $check
        		);

        		return $this->setResponseFormat(200,'',$data);
        	}
    	}
    	catch (TokenExpiredException $e) {
        	return $this->setResponseFormat(400, 'Token has been expired');
    	}
    	catch (TokenInvalidException $e) {
        	return $this->setResponseFormat(400, 'Invalid token sent');
    	}
    	catch (JWTException $e) {
        	return $this->setResponseFormat(400, 'Token is missing, please send token');
    	}
    }

    function updateUserProfile(Request $request){

    	try {
    	    if (! $user = JWTAuth::parseToken()->authenticate()) {
            	return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
        	}else{
                if(!isset($request->first_name) || empty($request->first_name) && !isset($request->last_name) || empty($request->last_name)){
                    $rules = [
                    'first_name'        => 'required|alpha|min:3',
                    'last_name'         => 'required|alpha|min:3',
                    'email'             => 'required|email',
                    'profile_picture'   => 'mimes:jpeg,jpg,png|max:1024'
                ];

                $messages = [
                    'first_name.required'   => $this->notificationMessage('update_profile_first_name_required','error'),
                    'first_name.alpha'      => $this->notificationMessage('update_profile_first_name_alpha','error'),
                    'first_name.min'        => $this->notificationMessage('update_profile_first_name_min','error'),
                    'last_name.required'    => $this->notificationMessage('update_profile_last_name_required','error'),
                    'last_name.alpha'       => $this->notificationMessage('update_profile_last_name_alpha','error'),
                    'last_name.min'         => $this->notificationMessage('update_profile_last_name_min','error'),
                    'email.required'        => $this->notificationMessage('update_profile_email_required','error'),
                    'email.email'           => $this->notificationMessage('update_profile_email_email','error'),
                    'profile_picture.mimes' => $this->notificationMessage('update_profile_profile_picture_mimes','error'),
                    'profile_picture.max'   => $this->notificationMessage('update_profile_profile_picture_max','error')

                ];
                }else{
                   $rules = [
                    'first_name'        => 'required|alpha|min:3|different:last_name',
                    'last_name'         => 'required|alpha|min:3|different:first_name',
                    'email'             => 'required|email',
                    'profile_picture'   => 'mimes:jpeg,jpg,png|max:1024'
                ];

                $messages = [
                    'first_name.required'   => $this->notificationMessage('update_profile_first_name_required','error'),
                    'first_name.alpha'      => $this->notificationMessage('update_profile_first_name_alpha','error'),
                    'first_name.min'        => $this->notificationMessage('update_profile_first_name_min','error'),
                    'first_name.different'  => $this->notificationMessage('update_profile_first_name_different','error'),
                    'last_name.required'    => $this->notificationMessage('update_profile_last_name_required','error'),
                    'last_name.alpha'       => $this->notificationMessage('update_profile_last_name_alpha','error'),
                    'last_name.min'         => $this->notificationMessage('update_profile_last_name_min','error'),
                    'last_name.different'   => $this->notificationMessage('update_profile_last_name_different','error'),
                    'email.required'        => $this->notificationMessage('update_profile_email_required','error'),
                    'email.email'           => $this->notificationMessage('update_profile_email_email','error'),
                    'profile_picture.mimes' => $this->notificationMessage('update_profile_profile_picture_mimes','error'),
                    'profile_picture.max'   => $this->notificationMessage('update_profile_profile_picture_max','error'),

                ];

                }



                $validator = Validator::make($request->all(), $rules,$messages);

                if(!$validator->fails()){

            		$userCount = User::where('email', '=', $request->email)->where('id','<>',$user->id)->get()->count();

            		//echo $userCount;die;

            		if($userCount == 0){

    	        		$updateUser = User::find($user->id);

    	        		$updateUser->first_name = $request->first_name;

    	        		$updateUser->last_name = $request->last_name;

    	        		$updateUser->email = $request->email;

                        //$updateUser->country_id = $request->country_id;

    	        		if($request->profile_picture){

    	        			if(file_exists(public_path().'/profile_pictures/'.$updateUser->profile_picture)){
    							@unlink(public_path().'/profile_pictures/'.$updateUser->profile_picture);
    	        			}

    	        		 	$ext = $request->file('profile_picture')->getClientOriginalExtension();

    	                   	$profilePictureName = 'profile-pic-'.$user->id.'-'.str_replace(' ','-',$user->first_name).'-'.time().'.'.$ext;

    	                    $request->file('profile_picture')->move(public_path("/profile_pictures/"), $profilePictureName);

    	                    $updateUser->profile_picture = $profilePictureName;

    	                }

    	        		$updateUser->save();

    	        		return $this->setResponseFormat(200, $this->notificationMessage('user_updated','success'));
    	        	}else{
    	        		return $this->setResponseFormat(400, $this->notificationMessage('email_already_exists','error'));
    	        	}
                }else{
                    // on error return error messages array
                    return $this->setResponseFormat(400, 'Validation Errors',$validator->messages());
                }

        	}
    	}
    	catch (TokenExpiredException $e) {
        	return $this->setResponseFormat(400, 'Token has been expired');
    	}
    	catch (TokenInvalidException $e) {
        	return $this->setResponseFormat(400, 'Invalid token sent');
    	}
    	catch (JWTException $e) {
        	return $this->setResponseFormat(400, 'Token is missing, please send token');
    	}
    }

    function updateUserProfilePicture(Request $request){

    	try {
    	    if (! $user = JWTAuth::parseToken()->authenticate()) {
            	return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
        	}else{

                $rules = [
                    'profile_picture'   => 'required|mimes:jpeg,jpg,png|max:1024'
                ];

                $messages = [
                    'profile_picture.required'  => $this->notificationMessage('update_profile_picture_profile_picture_required','error'),
                    'profile_picture.mimes'     => $this->notificationMessage('update_profile_picture_profile_picture_mimes','error'),
                    'profile_picture.max'       => $this->notificationMessage('update_profile_picture_profile_picture_max','error'),
                ];

                $validator = Validator::make($request->all(), $rules,$messages);

                if(!$validator->fails()){

            		$updateUser = User::find($user->id);

            		if($request->profile_picture){

            			if(file_exists(public_path().'/profile_pictures/'.$updateUser->profile_picture)){
    						@unlink(public_path().'/profile_pictures/'.$updateUser->profile_picture);
            			}

            		 	$ext = $request->file('profile_picture')->getClientOriginalExtension();

                       	$profilePictureName = 'profile-pic-'.$user->id.'-'.$user->first_name.'-'.time().'.'.$ext;

                        $request->file('profile_picture')->move(public_path("/profile_pictures/"), $profilePictureName);

                        $updateUser->profile_picture = $profilePictureName;

                    }

            		$updateUser->save();

            		return $this->setResponseFormat(200, $this->notificationMessage('user_profile_pic_updated','success'));
                }else{
                    // on error return error messages array
                    return $this->setResponseFormat(400, 'Validation Errors',$validator->messages());
                }

        	}
    	}
    	catch (TokenExpiredException $e) {
        	return $this->setResponseFormat(400, 'Token has been expired');
    	}
    	catch (TokenInvalidException $e) {
        	return $this->setResponseFormat(400, 'Invalid token sent');
    	}
    	catch (JWTException $e) {
        	return $this->setResponseFormat(400, 'Token is missing, please send token');
    	}
    }

    // User login system function which are used with registration
}
