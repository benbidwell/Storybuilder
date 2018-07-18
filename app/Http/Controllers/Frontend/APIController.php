<?php
namespace App\Http\Controllers\Frontend;
use Illuminate\Http\Request;
use App\Http\Controllers\FrontendController;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;
use Auth;
use Mail;
use App\Mail\UserRegistration;
use App\Mail\UserAdminRegistration;
use App\Mail\UserForgotPassword;
use App\Mail\UserChangePassword;
use App\Models\Country;
use App\Models\Story;
use Abraham\TwitterOAuth\TwitterOAuth;
use App\Models\AdminSetting;

class APIController extends FrontendController
{

   // protected $countries = array();

	//function for register api for front end
    public function register(Request $request)
    {
    	$input = $request->all();

    	$input['password'] = Hash::make($input['password']);


        try{
            if(!isset($request->first_name) || empty($request->first_name) && !isset($request->last_name) || empty($request->last_name)){


            $rules = [
                'first_name'    => 'required|alpha|min:3',
                'last_name'     => 'required|alpha|min:3',
                'email'         => 'required|email|unique:users',
                'password'      => 'required|min:6'
            ];

            $messages = [
                'first_name.required'   => $this->notificationMessage('register_first_name_required','error'),
                'first_name.alpha'      => $this->notificationMessage('register_first_name_alpha','error'),
                'first_name.min'        => $this->notificationMessage('register_first_name_min','error'),
                'last_name.required'    => $this->notificationMessage('register_last_name_required','error'),
                'last_name.alpha'       => $this->notificationMessage('register_last_name_alpha','error'),
                'last_name.min'         => $this->notificationMessage('register_last_name_min','error'),
                'email.required'        => $this->notificationMessage('register_email_required','error'),
                'email.email'           => $this->notificationMessage('register_email_email','error'),
                'email.unique'          => $this->notificationMessage('register_email_unique','error'),
                'password.required'     => $this->notificationMessage('register_password_required','error'),
                'password.min'          => $this->notificationMessage('register_password_min','error'),

            ];
        }else{
             $rules = [
                'first_name'    => 'required|alpha|min:3|different:last_name',
                'last_name'     => 'required|alpha|min:3|different:first_name',
                'email'         => 'required|email|unique:users',
                'password'      => 'required|min:6'
            ];

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
                'password.required'     => $this->notificationMessage('register_password_required','error'),
                'password.min'          => $this->notificationMessage('register_password_min','error'),

            ];
        }

            $validator = Validator::make($request->all(), $rules,$messages);

            if(!$validator->fails()){

                // user registered in database
                $user = User::create($input);

                //email template for front end user
                $userEmailTemplate = $this->EmailTemplate('user_created');

                $email = $user->email;

                //setting email content
                $emailContent = array('username'=>$user->first_name . ' '.$user->last_name,'userEmailTemplate'=>$userEmailTemplate->description);
                if($userEmailTemplate){
                //sending email to user
                Mail::to($email)->send(new UserRegistration($emailContent,$userEmailTemplate->subject));
                }
                //email template for back end user
                $userEmailTemplate = $this->EmailTemplate('admin_user_registration');

                $email = env('ADMIN_EMAIL_ID');

                $url =  url('') .'/admin';

                //setting email content
                $emailContent = array('username'=>$user->first_name . ' '.$user->last_name,'adminUrl'=> $url ,'userEmailTemplate'=>$userEmailTemplate->description);
                if($userEmailTemplate){
                //sending email to user
                Mail::to($email)->send(new UserAdminRegistration($emailContent,$userEmailTemplate->subject));
                }

                if($user){
                    /*
                    *  code for login and returning the json token
                    *  email and password is used as params
                    */
                    $credentials = ['email' => $request->input('email'), 'password' => $request->input('password')];

                    $token = $this->guard()->attempt($credentials);

                    /*
                    after login return token along with user register method
                    */
                    $data = array('is_authorized'=>'false');

                    // return successfull json on registration complete
                    return $this->setResponseFormat(200, $this->notificationMessage('register_successful','success'),$data,$token);

                }
            }else{
               // on error return error messages array
                return $this->setResponseFormat(400, 'Validation Errors',$validator->messages());
            }


        }catch(\Exception $e){

            return $this->setResponseFormat(400, $this->notificationMessage('registration_failed','error'));

        }

    }

    public function checkUser(Request $request){

        try{

                $userCount = User::where('email', '=', $request->email)->get()->count();


                if($userCount == 0){

                    $returnFormat = $this->setResponseFormat(200, $this->notificationMessage('email_not_exists','success'));

                    return $returnFormat;


                }else{

                    $returnFormat = $this->setResponseFormat(400, $this->notificationMessage('email_already_exists','error'));

                    return $returnFormat;
                }


        }catch(\Exception $e){

           return $this->setResponseFormat(400, $this->notificationMessage('check_user_email_failed','error'));

        }

    }

     public function forgotPassword(Request $request){


        try{

                $rules = [
                    'email'         => 'required|email',
                ];

                $messages = [
                    'email.required'        => $this->notificationMessage('forgot_email_required','error'),
                    'email.email'           => $this->notificationMessage('forgot_email_email','error'),

                ];

                $validator = Validator::make($request->all(), $rules,$messages);

                if(!$validator->fails()){

                    $user = User::where('email', '=', $request->email)->get()->first();

                    if (is_null($user)){

                        return $this->setResponseFormat(400, $this->notificationMessage('forgot_password_email_not_exists','error'));

                    }else{

                        //email template for front end user
                        $userEmailTemplate = $this->EmailTemplate('reset_password');

                        $email = $user->email;

                        $url =  url('') .'/reset_password/'.base64_encode($request->email);

                        //setting email content
                        $emailContent = array('username'=>$user->first_name . ' '.$user->last_name,'resetUrl'=> $url , 'userEmailTemplate'=>$userEmailTemplate->description);
                        if($userEmailTemplate){
                        //sending email to user
                        Mail::to($email)->send(new UserForgotPassword($emailContent,$userEmailTemplate->subject));
                        }
                        return $this->setResponseFormat(200, $this->notificationMessage('forgot_password_email_exists','success'));

                    }
                }else{
                     // on error return error messages array
                    return $this->setResponseFormat(400, 'Validation Errors',$validator->messages());
                }


        }catch(\Exception $e){

            return $this->setResponseFormat(400, $this->notificationMessage('check_user_email_failed','error'));

        }

    }


    public function resetPassword(Request $request){


        try{

                $rules = [
                     'password'              => 'required|min:6',
                     'password_confirmation' => 'required|same:password'
                ];

                $messages = [
                    'password.required'                 => $this->notificationMessage('reset_password_password_required','error'),
                    'password.min'                      => $this->notificationMessage('reset_password_password_min','error'),
                    'password_confirmation.required'    => $this->notificationMessage('reset_password_confirm_password_required','error'),
                    'password_confirmation.same'        => $this->notificationMessage('reset_password_confirm_password_confirmation','error'),
                ];

                $validator = Validator::make($request->all(), $rules,$messages);

                if(!$validator->fails()){

                    $user = User::where('email', htmlspecialchars(base64_decode($request->token)))->get()->first();

                    if (is_null($user)){

                        return $this->setResponseFormat(400, $this->notificationMessage('reset_password_token_miss_match','error'));


                    }else{
                        
                        User::where('email', '=', base64_decode($request->token))->update(['password' => Hash::make($request->password)]);
                        return $this->setResponseFormat(200, $this->notificationMessage('reset_password_done','success'));

                    }

                }else{
                    // on error return error messages array
                    return $this->setResponseFormat(400, 'Validation Errors',$validator->messages());
                }


        }catch(\Exception $e){

             return $this->setResponseFormat(400, $this->notificationMessage('reset_password_failed','error'));

        }

    }

    public function getCountries(){

        $countries = array();

        $tempCountries = Country::all();
        foreach($tempCountries as $country){
            $countries[$country->id] = $country->country_name;
        }

        return $this->setResponseFormat(200,'Countries list',$countries);
    }

    public function changePassword(Request $request){
        $fb = new \Facebook\Facebook([
            'app_id' => '{app-id}',
            'app_secret' => '{app-secret}',
            'default_graph_version' => 'v2.10',
            //'default_access_token' => '{access-token}', // optional
          ]);

        try {

                if (! $user = JWTAuth::parseToken()->authenticate()) {
                    return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
                }else{


                $rules = [
                         'old_password'          => 'required|min:6|different:password',
                         'password'              => 'required|min:6',
                         'password_confirmation' => 'required|same:password'
                ];

                $messages = [
                        'old_password.required'             => $this->notificationMessage('change_password_old_password_required','error'),
                        'old_password.min'                  => $this->notificationMessage('change_password_old_password_min','error'),
                        'old_password.different'            => $this->notificationMessage('change_password_old_password_different','error'),
                        'password.required'                 => $this->notificationMessage('change_password_password_required','error'),
                        'password.min'                      => $this->notificationMessage('change_password_password_min','error'),
                        'password_confirmation.required'    => $this->notificationMessage('change_password_confirm_password_required','error'),
                        'password_confirmation.same'        => $this->notificationMessage('change_password_confirm_password_confirmation','error'),
                ];


                $validator = Validator::make($request->all(), $rules,$messages);

                if(!$validator->fails()){
                        if(Hash::check($request->old_password,$user->password)){
                            $hashedpassword = Hash::make($request->password);
                            $updateUser = User::find($user->id);

                            $updateUser->password = $hashedpassword;

                            $updateUser->save();

                            //save password in admin table if user is admin

                            $emailid = $user->email;
                            $admin = Admin::where('email',$emailid)->first();
                            if($admin){
                              $admin->password = $hashedpassword;
                              $admin->save();
                            }

                        //email template for front end user
                        $userEmailTemplate = $this->EmailTemplate('change_password');

                        $email = $user->email;

                        //setting email content
                        $emailContent = array('username'=>$user->first_name . ' '.$user->last_name,'userEmailTemplate'=>$userEmailTemplate->description);

                        if($userEmailTemplate){
                        //sending email to user
                        Mail::to($email)->send(new UserChangePassword($emailContent,$userEmailTemplate->subject));
                        }
                        return $this->setResponseFormat(200, $this->notificationMessage('password_changed','success'));

                    }else{
                        return $this->setResponseFormat(400, $this->notificationMessage('old_password_incorrect','error'));

                    }


                }
                else{
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

    protected function guard()
    {
        return Auth::guard('members');
    }

    public function getCurlResponse(Request $request){

        $url = $request->url;
        if($request->has('access_token')){
          $url.='?access_token='.$request->access_token;
        }

        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $output=curl_exec($ch);

        curl_close($ch);

        return $output;
    }

    public function twitterVideoUpload(Request $request){
        // $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token, $access_token_secret);
        // $content = $connection->get("account/verify_credentials");

        // $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token, $access_token_secret);
        // $media1 = $connection->upload('media/upload', ['media' => '/path/to/file/kitten1.jpg']);
        // $media2 = $connection->upload('media/upload', ['media' => '/path/to/file/kitten2.jpg']);
        // $parameters = [
        //     'status' => 'Meow Meow Meow',
        //     'media_ids' => implode(',', [$media1->media_id_string, $media2->media_id_string])
        // ];
        // $result = $connection->post('statuses/update', $parameters);


        // $url = $request->url;
        // if($request->has('access_token')){
        //   $url.='?access_token='.$request->access_token;
        // }

        // $ch = curl_init();

        // curl_setopt($ch,CURLOPT_URL,$url);
        // curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // $output=curl_exec($ch);

        // curl_close($ch);

        // return $output;
        \Codebird\Codebird::setConsumerKey('JhuPb38KHgYgUSbMXBB8rmpUD', 'Dj33EcXZEHHqgLOe27hj0t5gQKVtnt5gx9PbhIep1C7bi7lIqU
        ');
        $cb = \Codebird\Codebird::getInstance();

        $cb->setToken('892095854194065409-qNH4NFKdXQul3QatLfFtVm7y0CFt0vf', '9el0sAgjLDJGTyNja5j94xkkp5vRnMGJ8k74vsStsOx4v
        ');
      
        $file       = public_path($request->video_url);
        $size_bytes = filesize($file);
        $fp         = fopen($file, 'r');
        
        // INIT the upload

        $reply = $cb->media_upload([
        'command'     => 'INIT',
        'media_type'  => 'video/mp4',
        'total_bytes' => $size_bytes
        ]);
  
        if(!@$reply->media_id_string){
            return ['reply'=>$reply];

        }
        $media_id = $reply->media_id_string;

        // APPEND data to the upload

        $segment_id = 0;

        while (! feof($fp)) {
        $chunk = fread($fp, 1048576); // 1MB per chunk for this sample

        $reply = $cb->media_upload([
            'command'       => 'APPEND',
            'media_id'      => $media_id,
            'segment_index' => $segment_id,
            'media'         => $chunk
        ]);

        $segment_id++;
        }

        fclose($fp);

        // FINALIZE the upload

        $reply = $cb->media_upload([
        'command'       => 'FINALIZE',
        'media_id'      => $media_id
        ]);

        var_dump($reply);

        if ($reply->httpstatus < 200 || $reply->httpstatus > 299) {
        die();
        }

        // if you have a field `processing_info` in the reply,
        // use the STATUS command to check if the video has finished processing.

        // Now use the media_id in a Tweet
        $reply = $cb->statuses_update([
        'status'    => 'Twitter now accepts video uploads.',
        'media_ids' => $media_id
        ]);


    }

    public function postCurlResponse(Request $request){

        $url = $request->url;
        $arr=array();
        if($request->has('Authorization')){
            array_push($arr,"authorization:".$request->Authorization);
            if($request->command == 'init') {
                array_push($arr,"content-type: application/x-www-form-urlencoded");
            }
            elseif($request->command == 'append') {
                // array_push($arr,"content-type: multipart/form-data");
            }
            else {
                array_push($arr,"content-type: application/x-www-form-urlencoded");
            }
       
        }
        //print_r($request->command);
  
         $curl = curl_init();


        curl_setopt_array($curl, array(
            CURLOPT_URL =>  $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $request->post_data,
            CURLOPT_HTTPHEADER => $arr,
        ));
  
        $output=curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
    
        return [$output,$httpcode, $arr,$request->post_data];
    }
    public function getCurlResponseTwit(Request $request){

        $url = $request->url;
        $arr=array();
        if($request->has('Authorization')){
            array_push($arr,"authorization:".$request->Authorization);
        }
        array_push($arr,"content-type: application/x-www-form-urlencoded");
       
        //print_r($request->command);
  
         $curl = curl_init();


        curl_setopt_array($curl, array(
            CURLOPT_URL =>  $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => $arr,
        ));
  
        $output=curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
    
        return [$output,$httpcode, $arr];


    }

    public function postCurlResponseFB(Request $request){

        
        $fb = new \Facebook\Facebook([
            'app_id' => '909742709184337',
            'app_secret' => '94cbbca16f540bdd5f9244dc517216a0',
            'default_graph_version' => 'v2.11',
            'http_client_handler'=>'stream'
            ]);
            
            $helper = $fb->getJavaScriptHelper();
    
          $data = [
            'title' => @$request->formdata['title'] ,
            'description' => (@$request->formdata['story_des'].'  '.@$request->formdata['pageurl']) ,
            'source' => $fb->videoToUpload(base_path('public'.$request->video_url)),
          ];
        
          try {
            $accessToken = $helper->getAccessToken();
            $response = $fb->post('/me/videos', $data, $accessToken);
 
            $graphNode = $response->getGraphNode();
      
            if(@$request->formdata['story_id']){
                $story=Story::find(@$request->formdata['story_id']);
                $story->facebook=$graphNode['id'];
                $story->save();
            }
          } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
          } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
          }

        // $url = $request->url;
        // $arr=array();
        // if($request->has('Authorization')){
        //     array_push($arr,"authorization:".$request->Authorization);
        //     if($request->command == 'init') {
        //         array_push($arr,"content-type: application/x-www-form-urlencoded");
        //     }
        //     elseif($request->command == 'append') {
        //         // array_push($arr,"content-type: multipart/form-data");
        //     }
        //     else {
        //         array_push($arr,"content-type: application/x-www-form-urlencoded");
        //     }
       
        // }
        // //print_r($request->command);
  
        //  $curl = curl_init();


        // curl_setopt_array($curl, array(
        //     CURLOPT_URL =>  $url,
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => "",
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 30,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => "POST",
        //     CURLOPT_POSTFIELDS => $request->post_data,
        //     CURLOPT_HTTPHEADER => $arr,
        // ));
  
        // $output=curl_exec($curl);
        // $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        // curl_close($curl);
    
        // return [$output,$httpcode, $arr,$request->post_data];
    }
    
    public function getCurlResponseInsta(Request $request){

        $url = $request->url;
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET"
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $resp = json_decode($response);
        $data = @$resp->data;
        if(@$resp->pagination->next_url!=''){
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => @$resp->pagination->next_url,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "GET"
            ));
            $response = curl_exec($curl);
            $resp2 = json_decode($response);
            curl_close($curl);
            if(@$resp2->data!=''){
                $data =array_merge($data, @$resp2->data);
            }


            if(@$resp2->pagination->next_url!=''){
                $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => @$resp2->pagination->next_url,
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 30,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "GET"
                ));
                $response = curl_exec($curl);
                $resp3 = json_decode($response);
                curl_close($curl);
                if(@$resp3->data!=''){
                    $data =array_merge($data, @$resp3->data);
                }
                if(@$resp3->pagination->next_url!=''){
                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                      CURLOPT_URL => @$resp3->pagination->next_url,
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => "",
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 30,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => "GET"
                    ));
                    $response = curl_exec($curl);
                    $resp4 = json_decode($response);
                    curl_close($curl);
                    if(@$resp3->data!=''){
                        $data =array_merge($data, @$resp4->data);
                    }
                    if(@$resp4->pagination->next_url!=''){
                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                          CURLOPT_URL => @$resp4->pagination->next_url,
                          CURLOPT_RETURNTRANSFER => true,
                          CURLOPT_ENCODING => "",
                          CURLOPT_MAXREDIRS => 10,
                          CURLOPT_TIMEOUT => 30,
                          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                          CURLOPT_CUSTOMREQUEST => "GET"
                        ));
                        $response = curl_exec($curl);
                        $resp4 = json_decode($response);
                        curl_close($curl);
                        if(@$resp4->data!=''){
                            $data =array_merge($data, @$resp4->data);
                        }
                    }
                }
            }
        }
        if ($err) {
          echo "cURL Error #:" . $err;
        } else {
          return $data;
        }
        // $ch = curl_init();
        //
        // curl_setopt($ch,CURLOPT_URL,$url);
        // curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        // $output=curl_exec($ch);
        //
        // curl_close($ch);
        // print_r($output);
        // return $output;
    }

    public function getSettings(Request $request){
        $data=AdminSetting::first();
        return $this->setResponseFormat(200, '',$data);
    }
}
