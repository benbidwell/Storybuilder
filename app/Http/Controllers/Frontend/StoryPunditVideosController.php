<?php

namespace App\Http\Controllers\FrontEnd;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontendController;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;
use App\Models\StoryPunditVideo;
use App\Models\Story;
use Carbon\Carbon;
use Mail;
use App\Mail\PunditVideoPublished;
use App\Mail\PunditActivationLink;
use Hash;
use App\Models\User;
use Validator;



class StoryPunditVideosController extends FrontendController
{
    public function addAudioToVideo(Request $request){

        $punditVideo = $this->_createVideo($request->audio,$request->story_id);
        $data = array('video_url'=> $punditVideo['video_url'],'background_sound_file_url'=>$punditVideo['audio_url']);
        return $this->setResponseFormat(200, $this->notificationMessage('pundit_voice_added_successfully','success'),$data);
    }

    public function _randomPassword($length) {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < $length; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    public function addAudioToVideoUserNotLoggedIn(Request $request){
       try{

            $newPassword = $this->_randomPassword(10);

            $input['password'] = Hash::make($newPassword);

            $input['delete_status'] = 0;

            $rules = [
                'first_name'    => 'required|alpha|min:3|different:last_name',
                'last_name'     => 'required|alpha|min:3|different:first_name',
                'email'         => 'required|email|unique:users',
            ];

            $input['first_name'] = $request->first_name;

            $input['last_name']  = $request->last_name;

            $input['email']      = $request->email;

            $messages = [
                'first_name.required'   => $this->notificationMessage('register_first_name_required','error'),
                'first_name.alpha'      => $this->notificationMessage('register_first_name_alpha','error'),
                'first_name.min'        => $this->notificationMessage('register_first_name_min','error'),
                'first_name.different'  => $this->notificationMessage('register_first_name_different','error'),
                'last_name.required'    => $this->notificationMessage('register_last_name_required','error'),
                'last_name.alpha'       => $this->notificationMessage('register_last_name_alpha','error'),
                'last_name.min'         => $this->notificationMessage('register_last_name_min','error'),
                'last_name.different'   => $this->notificationMessage('register_last_name_different','error'),
                'email.required'        => $this->notificationMessage('register_email_required','error'),
                'email.email'           => $this->notificationMessage('register_email_email','error'),
                'email.unique'          => $this->notificationMessage('register_email_unique','error'),
            ];


            $validator = Validator::make($input, $rules,$messages);

            if(!$validator->fails()){

                // user registered in database
                $user = User::create($input);

                $punditVideo = $this->_createVideo($request->audio,$request->story_id,$request->video_title,$user->id);

                //echo $punditVideo->id;
                $url =  url('') .'/pundit_activate_account/'.base64_encode($user->id).'/'.base64_encode($user->email).'/'.base64_encode($punditVideo->id);

                // Email to user for publishing the story
                $userEmailTemplate = $this->EmailTemplate('pundit_verification_email');

                $email = $user->email;

                //setting email content
                $emailContent = array('username'=>$user->first_name . ' '.$user->last_name,'user_id' => $user->email,'password'=> $newPassword,'verification_link' => $url,'userEmailTemplate'=>$userEmailTemplate->description);

                //sending email to user
                if($userEmailTemplate){
                Mail::to($email)->send(new PunditActivationLink($emailContent,$userEmailTemplate->subject));
                 }
                return $this->setResponseFormat(200, $this->notificationMessage('pundit_user_created','success'));

            }else{
               // on error return error messages array
                return $this->setResponseFormat(400, 'Validation Errors',$validator->messages());
            }
        }
        catch(\Exception $e){

            return $this->setResponseFormat(400, $this->notificationMessage('registration_failed','error'));

        }
    }

    public function punditActivateAccount(Request $request){
         try{

            $user = User::where('id',$request->user_id)->first();
  
            if(count((array)$user)==0){
                return  $this->setResponseFormat(400, $this->notificationMessage('User_already_activated','error'));
            }
            $user->authorized = 1;

            $user->save();
          
            $story_pundit_video = StoryPunditVideo::find($request->pundit_id);

            $story_pundit_video->publish_status = 1;

            $story_pundit_video->publish_date = date('Y-m-d H:i:s');

            $story_pundit_video->save();

            $dt = Carbon::now();

            // Email to user for publishing the story
            $userEmailTemplate = $this->EmailTemplate('pundit_published_user');

            $email = $user->email;

            //setting email content
            $emailContent = array('username'=>$user->first_name . ' '.$user->last_name,'videoname' => $story_pundit_video->video_name,'created_date'=> $dt->toDayDateTimeString(),'userEmailTemplate'=>$userEmailTemplate->description);

            //sending email to user
            if($userEmailTemplate){
            Mail::to($email)->send(new PunditVideoPublished($emailContent,$userEmailTemplate->subject));
             }
            //Email to Admin for publishing the story
            $userEmailTemplate = $this->EmailTemplate('pundit_published_admin');

            $email = env('ADMIN_EMAIL_ID');

            //setting email content
            $emailContent = array('username'=>$user->first_name . ' '.$user->last_name,'videoname' => $story_pundit_video->video_name,'created_date'=> $dt->toDayDateTimeString(),'userEmailTemplate'=>$userEmailTemplate->description);

            //sending email to admin
            if($userEmailTemplate){
            Mail::to($email)->send(new PunditVideoPublished($emailContent,$userEmailTemplate->subject));
            }
           
            return $this->setResponseFormat(200, $this->notificationMessage('pundit_published_successfully','success'),$story_pundit_video);

     }
       catch(\Exception $e){

            return $this->setResponseFormat(400, $this->notificationMessage('pundit_activation_failed','error'));

       }
   }

    public function _createVideo($audio,$story_id,$video_title='',$user_id=''){

        /*
        * Saving the audio file
        */

        $folderName = '/story_audio/story_'.$story_id;

        $folderPath = public_path().$folderName;

        if(isset($audio)){

            if (!file_exists($folderPath)) {
                mkdir($folderPath, 0777, true);
            }

            $newFileName = time().'-'.$audio->getClientOriginalName();

            $audio->move(public_path($folderName), $newFileName);
        }

        /*
        * clearing the sound from earlier video
        */

        $videofolderName = '/story_videos/story_'.$story_id;

        $videoFolderPath = public_path().$videofolderName;

        $story = Story::find($story_id);

        $video = $videoFolderPath.'/'.$story->story_published_video_url;

        $videoWithoutSound = $videoFolderPath.'/'.time().'-ws.mp4';

        $ffmpeg_command = "ffmpeg -i ".$video." -c copy -an ".$videoWithoutSound;

        shell_exec($ffmpeg_command);
      

        /*
        * adding the sound to video without sound
        */


        $punditFolderPath = public_path().'/story_videos/story_'.$story_id;

        if (!file_exists($punditFolderPath)) {
            mkdir($punditFolderPath, 0777, true);
        }
        
        $punditVideo = time().'.mp4';

        $punditFilePath = $punditFolderPath.'/'.$punditVideo;

        $backgroundSound = $folderPath.'/'.$newFileName;

        //$ffmpeg_command = "ffmpeg -i ". $videoWithoutSound ." -i ".$backgroundSound ." -codec copy -shortest ".$punditFilePath;

        //$ffmpeg_command = "ffmpeg -i ". $videoWithoutSound ." -i ".$backgroundSound ." -c:v copy -af apad -shortest ".$punditFilePath;

        //$ffmpeg_command = "ffmpeg -i ". $videoWithoutSound ." -i ".$backgroundSound ." -c:v copy -af apad -shortest -strict -2 ".$punditFilePath;

        $ffmpeg_command = "ffmpeg -i ". $videoWithoutSound ." -i ".$backgroundSound ." -map 0:v -map 1:a -c:v copy -strict -2  ".$punditFilePath;
        
       // echo $ffmpeg_command; die;
        shell_exec($ffmpeg_command);
        // $story_pundit_video = new StoryPunditVideo();

        // $story_pundit_video->user_id = $user_id;

        // $story_pundit_video->video_name = $video_title;

        // $story_pundit_video->story_id = $story_id;

        // $story_pundit_video->new_video_url = $punditVideo;

        // $story_pundit_video->background_sound_file_url = $newFileName;

        // $story_pundit_video->status = 1;

        // $story_pundit_video->save();
        // dd($story_pundit_video);
        return  ['video_url'=>$punditVideo,'audio_url'=>$newFileName];
    }

    // public function punditPublishVideo(Request $request){

    //     try {

    //         if (! $user = JWTAuth::parseToken()->authenticate()) {
    //             return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
    //         }else{

    //             $story_pundit_video = StoryPunditVideo::find($request->story_pundit_id);

    //             $story_pundit_video->publish_status = 1;

    //             $story_pundit_video->publish_date = date('Y-m-d H:i:s');

    //             $story_pundit_video->save();

    //             $dt = Carbon::now();

    //             // Email to user for publishing the story
    //             $userEmailTemplate = $this->EmailTemplate('pundit_published_user');

    //             $email = $user->email;

    //             //setting email content
    //             $emailContent = array('username'=>$user->first_name . ' '.$user->last_name,'videoname' => $story_pundit_video->video_name,'created_date'=> $dt->toDayDateTimeString(),'userEmailTemplate'=>$userEmailTemplate->description);

    //             //sending email to user
    //             Mail::to($email)->send(new PunditVideoPublished($emailContent,$userEmailTemplate->subject));

    //             //Email to Admin for publishing the story
    //             $userEmailTemplate = $this->EmailTemplate('pundit_published_admin');

    //             $email = env('ADMIN_EMAIL_ID');

    //             //setting email content
    //             $emailContent = array('username'=>$user->first_name . ' '.$user->last_name,'videoname' => $story_pundit_video->video_name,'created_date'=> $dt->toDayDateTimeString(),'userEmailTemplate'=>$userEmailTemplate->description);

    //             //sending email to admin
    //             Mail::to($email)->send(new PunditVideoPublished($emailContent,$userEmailTemplate->subject));

    //             return $this->setResponseFormat(200, $this->notificationMessage('pundit_published_successfully','success'));

    //         }
    //     }
    //     catch (TokenExpiredException $e) {
    //          return $this->setResponseFormat(400, 'Token has been expired');
    //     }
    //     catch (TokenInvalidException $e) {
    //         return $this->setResponseFormat(400, 'Invalid token sent');
    //     }
    //     catch (JWTException $e) {
    //         return $this->setResponseFormat(400, 'Token is missing, please send token');
    //     }
    // }
    public function punditPublishVideoLoggedUser(Request $request){
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));

            }else{

                $rules = [
                    'video_title'  =>  'required',
                ];

                $input['video_title'] = $request->video_title;
                $messages = [
                    'video_title.required'          => $this->notificationMessage('video_title_required','error'),
                ];
                $validator = Validator::make($input, $rules,$messages);

                if(!$validator->fails()){
                    $story_pundit_video = new StoryPunditVideo();

                    $story_pundit_video->user_id = $user->id;

                    $story_pundit_video->video_name = $request->video_title;

                    $story_pundit_video->story_id = $request->story_id;

                    $story_pundit_video->new_video_url = $request->video_url;

                    $story_pundit_video->background_sound_file_url = $request->background_sound_file_url;

                    $story_pundit_video->publish_date = date('Y-m-d H:i:s');

                    $story_pundit_video->status = 1;

                    $story_pundit_video->publish_status = 1;

                    $story_pundit_video->save();

                    $dt = Carbon::now();

                    // Email to user for publishing the story
                    $userEmailTemplate = $this->EmailTemplate('pundit_published_user');

                    $email = $user->email;

                    //setting email content
                    $emailContent = array('username'=>$user->first_name . ' '.$user->last_name,'videoname' => $story_pundit_video->video_name,'created_date'=> $dt->toDayDateTimeString(),'userEmailTemplate'=>$userEmailTemplate->description);

                    //sending email to user
                    //Mail::to($email)->send(new PunditVideoPublished($emailContent,$userEmailTemplate->subject));

                    //Email to Admin for publishing the story
                    $userEmailTemplate = $this->EmailTemplate('pundit_published_admin');

                    $email = env('ADMIN_EMAIL_ID');

                    //setting email content
                    $emailContent = array('username'=>$user->first_name . ' '.$user->last_name,'videoname' => $story_pundit_video->video_name,'created_date'=> $dt->toDayDateTimeString(),'userEmailTemplate'=>$userEmailTemplate->description);

                    //sending email to admin
                    //Mail::to($email)->send(new PunditVideoPublished($emailContent,$userEmailTemplate->subject));

                    return $this->setResponseFormat(200, $this->notificationMessage('pundit_published_successfully','success'),$story_pundit_video);
                }
                else {
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
    public function punditPublishVideo(Request $request){

        $newPassword = $this->_randomPassword(10);

        $input['password'] = Hash::make($newPassword);

        $input['delete_status'] = 0;

        $rules = [
            'first_name'    => 'required|alpha|min:3|different:last_name',
            'last_name'     => 'required|alpha|min:3|different:first_name',
            'email'         => 'required|email|unique:users',
            'video_title'  =>  'required',
        ];
        
        $input['first_name'] = $request->first_name;
        $input['video_title'] = $request->video_title;
        $input['last_name']  = $request->last_name;

        $input['email']      = $request->email;

        $messages = [
            'first_name.required'   => $this->notificationMessage('register_first_name_required','error'),
            'first_name.alpha'      => $this->notificationMessage('register_first_name_alpha','error'),
            'first_name.min'        => $this->notificationMessage('register_first_name_min','error'),
            'first_name.different'  => $this->notificationMessage('register_first_name_different','error'),
            'last_name.required'    => $this->notificationMessage('register_last_name_required','error'),
            'last_name.alpha'       => $this->notificationMessage('register_last_name_alpha','error'),
            'last_name.min'         => $this->notificationMessage('register_last_name_min','error'),
            'last_name.different'   => $this->notificationMessage('register_last_name_different','error'),
            'email.required'        => $this->notificationMessage('register_email_required','error'),
            'email.email'           => $this->notificationMessage('register_email_email','error'),
            'email.unique'          => $this->notificationMessage('register_email_unique','error'),
            'video_title.required'          => $this->notificationMessage('video_title_required','error'),
        ];

        
        $validator = Validator::make($input, $rules,$messages);
        
        if(!$validator->fails()){

            // user registered in database
            $user = User::create($input);

            //$punditVideo = $this->_createVideo($request->audio,$request->story_id,$request->video_title,$user->id);
            $story_pundit_video = new StoryPunditVideo();

            $story_pundit_video->user_id = $user->id;

            $story_pundit_video->video_name = $request->video_title;

            $story_pundit_video->story_id = $request->story_id;

            $story_pundit_video->new_video_url = $request->video_url;

            $story_pundit_video->background_sound_file_url = $request->background_sound_file_url;

            $story_pundit_video->publish_date = date('Y-m-d H:i:s');

            $story_pundit_video->status = 1;

            $story_pundit_video->publish_status = 0;
            $videoFolderPath = public_path()."/story_videos/story_".$request->story_id.'/'.$request->video_url;
            $story_pundit_video->video_size= filesize($videoFolderPath);
            $story_pundit_video->save();

            //echo $punditVideo->id;
            $url =  url('') .'/pundit-activate-account/'.base64_encode($user->id).'/'.base64_encode($user->email).'/'.base64_encode($story_pundit_video->id);

            // Email to user for publishing the story
            $userEmailTemplate = $this->EmailTemplate('pundit_verification_email');

            $email = $user->email;

            //setting email content
            $emailContent = array('username'=>$user->first_name . ' '.$user->last_name,'user_id' => $user->email,'password'=> $newPassword,'verification_link' => $url,'userEmailTemplate'=>$userEmailTemplate->description);

            //sending email to
            if($userEmailTemplate){
            Mail::to($email)->send(new PunditActivationLink($emailContent,$userEmailTemplate->subject));
            }
            return $this->setResponseFormat(200, $this->notificationMessage('pundit_user_created','success'),$story_pundit_video);

        }else{
            // on error return error messages array
            return $this->setResponseFormat(400, 'Validation Errors',$validator->messages());
        }
    }
    public function popularVideo(Request $request){
      
        $limit = @$request['limit'];
        $offset = @$request['offset'];
        if($limit == ''){
            $limit = 2;
        }
        $story_pundit_videos = StoryPunditVideo::with('user')->orderBy('id', 'DESC')->where('publish_status',1)->limit($limit)->offset($offset)->get();
        return $this->setResponseFormat(200, 'All Published Pundit Videos',array('pundit_published_videos' => $story_pundit_videos), NULL, NULL, 0);

    }
    public function allPunditPublishedVideos(){
      
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                 return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
             }else{

                  $story_pundit_videos = StoryPunditVideo::orderBy('id', 'DESC')->where('publish_status',1)->get();

                 return $this->setResponseFormat(200, 'All Published Pundit Videos',array('pundit_published_videos' => $story_pundit_videos), NULL, NULL, 0);

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

    public function allPunditUnPublishedVideos(){

        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                 return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
             }else{

                 $story_pundit_videos = StoryPunditVideo:: orderBy('id', 'DESC')->where('publish_status',0)->get();

                 return $this->setResponseFormat(200, 'All Un Published Pundit Videos',array('pundit_un_published_videos' => $story_pundit_videos), NULL, NULL, 0);

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

    public function allPunditPublishedVideosByStory(Request $request,$story_id){
       
        $limit = @$request['limit'];
        $offset = @$request['offset'];
        if($limit == ''){
            $limit = 2;
        }
      
        $story_pundit_videos = StoryPunditVideo:: with('user')->where('publish_status',1)->where('story_id',$story_id)->offset($offset)->limit($limit)->get();

        return $this->setResponseFormat(200, 'All Un Published Pundit Videos', $story_pundit_videos, NULL, NULL, 0);
    }

    public function allPunditPublishedVideosByPundit($pundit_id){

        $story_pundit_videos = StoryPunditVideo:: orderBy('id', 'DESC')->where('publish_status',1)->where('user_id',$pundit_id)->get();

        return $this->setResponseFormat(200, 'All Un Published Pundit Videos',array('pundit_un_published_videos' => $story_pundit_videos), NULL, NULL, 0);
    }

    public function getPunditStory($story_id,$pundit_story_id){
        
        $story_pundit_videos = StoryPunditVideo::where('publish_status',1)
                                                ->where('story_id',$story_id)
                                                ->where('id',$pundit_story_id)
                                                ->first();

        return $this->setResponseFormat(200, 'Pundit Story',$story_pundit_videos, NULL, NULL, 0);
    }


    public function postVideoToFacebook(Request $request){
        $fb = new Facebook\Facebook([
            'app_id' => '344143862712960',
            'app_secret' => '{app-secret}',
            'default_graph_version' => 'v2.2',
            ]);
          
          $data = [
            'title' => 'My Foo Video',
            'description' => 'This video is full of foo and bar action.',
            'source' => $fb->videoToUpload('/path/to/foo_bar.mp4'),
          ];
          
          try {
            $response = $fb->post('/me/videos', $data, 'user-access-token');
          } catch(Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
          } catch(Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
          }
          
          $graphNode = $response->getGraphNode();
          var_dump($graphNode);
          
          echo 'Video ID: ' . $graphNode['id'];
    }

    public function pundit_story_update(Request $request){
        try {
            // if (! $user = JWTAuth::parseToken()->authenticate()) {
            //      return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
            //  }else{

                $story_pundit_videos = StoryPunditVideo:: find($request->input('id'));
   
                if($request->has('facebook')){
                    $story_pundit_videos->facebook = $request->input('facebook');
                }
                if($request->has('youtube')){
                    $story_pundit_videos->youtube = $request->input('youtube');
                }
                if($request->has('twitter')){
                    $story_pundit_videos->twitter = $request->input('twitter');
                }
                
                $story_pundit_videos->save();

                return $this->setResponseFormat(200, 'Video posted successfully',array('pundit_published_videos' => $story_pundit_videos), NULL, NULL, 0);

          //   }
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


}
