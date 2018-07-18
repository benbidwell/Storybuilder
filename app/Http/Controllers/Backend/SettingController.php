<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BackendController;
use Validator;
use App\Models\AdminSetting;
use Mail;
use App\Mail\AdminSettingSaved;
class SettingController extends BackendController
{
	//display setting page
    public function adminSettings()
    {
    	$adminsetting = AdminSetting:: orderBy('id', 'DESC')->get();
    	$count = AdminSetting:: orderBy('id', 'DESC')->count();
    	if($count != 0){
    		return view('admin/settings/edit')->with('adminsetting',$adminsetting);
    	}
        else{
        	return view('admin/settings/index');
        }
    }

    //save settings
    public function storeSettings(Request $request)
  {
    //   $rules = [
    //           'smtp_port'        => 'required',
    //           'smtp_host'        => 'required',
    //           'smtp_username'    => 'required', 
    //           'smtp_password'   => 'required', 
    //           'google_analytics_code'   => 'required', 
    //           'pundit_title_text'   => 'required', 
    //           'facebook_pixel_code'   => 'required', 
    //           'trasition_time'   => 'required',                
    //   ];

    //   $messages = [
    //         'smtp_port.required'   => $this->notificationMessage('smtp_port_required','error'),
    //         'smtp_host.required'      => $this->notificationMessage('smtp_host_required','error'),      
    //         'smtp_username.required'       => $this->notificationMessage('smtp_username_required','error'),
    //         'smtp_password.required'        => $this->notificationMessage('smtp_password_required','error'),
    //         'google_analytics_code.required' => $this->notificationMessage('google_analytics_code_required','error'),  
    //          'pundit_title_text.required' => $this->notificationMessage('pundit_title_text_required','error'),
    //           'facebook_pixel_code.required' => $this->notificationMessage('facebook_pixel_code_required','error'),  
    //         'trasition_time.required' => $this->notificationMessage('trasition_time_required','error')    
    //   ];

    //     $validator = Validator::make($request->all(), $rules,$messages);

    //     // if($validator->fails())
    //     // {
    //     //      return Redirect::to('admin/settings')->withErrors($validator);               
    //     // }
    //     // else
        { 
           
            $adminsetting = new AdminSetting();
            $adminsetting->smtp_port = $request->smtp_port;
            $adminsetting->smtp_host = $request->smtp_host;
            $adminsetting->smtp_username = $request->smtp_username;
            $adminsetting->smtp_password = $request->smtp_password;
            $adminsetting->google_analytics_code = $request->google_analytics_code;
            $adminsetting->pundit_title_text = $request->pundit_title_text;
            $adminsetting->facebook_pixel_code = $request->facebook_pixel_code;
            $adminsetting->trasition_time = $request->trasition_time;
            $adminsetting->save();   

            

            $email = env('ADMIN_EMAIL_ID');
            $email_body = $this->emailTemplate('admin_setting_saved');
            $email_content = array('content' => $adminsetting,'body'=> $email_body);
            if($email_body){
            Mail::to($email)->send(new AdminSettingSaved($email_content,$email_body->subject));
            }  
            return redirect('/admin/settings')->with('success','Admin Setting Saved Successfully');  
        } 
  }

  //edit settings

  public function updateSettings(Request $request, $id){
	// 	$rules = [
    //         'smtp_port'        => 'required',
    //         'smtp_host'        => 'required',
    //         'smtp_username'    => 'required', 
    //         'smtp_password'   => 'required', 
    //         'google_analytics_code'   => 'required', 
    //         'pundit_title_text'   => 'required', 
    //         'facebook_pixel_code'   => 'required', 
    //         'trasition_time'   => 'required',                
    // ];

    //   $messages = [
    //         'smtp_port.required'   => $this->notificationMessage('smtp_port_required','error'),
    //         'smtp_host.required'      => $this->notificationMessage('smtp_host_required','error'),      
    //         'smtp_username.required'       => $this->notificationMessage('smtp_username_required','error'),
    //         'smtp_password.required'        => $this->notificationMessage('smtp_password_required','error'),
    //         'google_analytics_code.required' => $this->notificationMessage('google_analytics_code_required','error'),  
    //          'pundit_title_text.required' => $this->notificationMessage('pundit_title_text_required','error'),
    //           'facebook_pixel_code.required' => $this->notificationMessage('facebook_pixel_code_required','error'),  
    //         'trasition_time.required' => $this->notificationMessage('trasition_time_required','error')    
    //   ];

    //     $validator = Validator::make($request->all(), $rules,$messages);

    //     if($validator->fails())
    //     {
    //          return Redirect::to('admin/settings')->withErrors($validator);               
    //     }
    //     else
        { 
        
        	$adminsetting = AdminSetting::find($id);
            $adminsetting->smtp_port = $request->smtp_port;
            if($request->smtp_port){
                $env_update = $this->changeEnv([
                    'MAIL_PORT'   => $request->smtp_port
                ]);
            }
            
            $adminsetting->smtp_host = $request->smtp_host;
            if($request->smtp_host){
                $env_update = $this->changeEnv([
                    'MAIL_HOST'   => $request->smtp_host
                ]);
            }
            $adminsetting->smtp_username = $request->smtp_username;
            if($request->smtp_username){
                $env_update = $this->changeEnv([
                    'MAIL_USERNAME'   => $request->smtp_username
                ]);
            }
            $adminsetting->smtp_password = $request->smtp_password;
            if($request->smtp_password){
                $env_update = $this->changeEnv([
                    'MAIL_PASSWORD'   => $request->smtp_password
                ]);
            }
            
            $adminsetting->google_analytics_code = $request->google_analytics_code;
            if($request->google_analytics_code){
                $env_update = $this->changeEnv([
                    'GOOGLE_TRACKING_ID'   => $request->google_analytics_code
                ]);
            }
            $adminsetting->pundit_title_text = $request->pundit_title_text;
            $adminsetting->facebook_pixel_code = $request->facebook_pixel_code;
            $adminsetting->trasition_time = $request->trasition_time;
            $adminsetting->is_watermark=$request->is_watermark?1:0;
            $adminsetting->is_authenticate=$request->is_authenticate?1:0;
            $adminsetting->save();   

            $email = env('ADMIN_EMAIL_ID');
            if($email == ''){
              $email = 'developer.walkwel@gmail.com';
            }
            $email_body = $this->emailTemplate('admin_setting_saved');
            $email_content = array('content' => $adminsetting,'body'=> $email_body);
            if($email_body){
                Mail::to($email)->send(new AdminSettingSaved($email_content,$email_body->subject));  
            }
            return redirect('/admin/settings')->with('success','Admin Setting Saved Successfully');  
        } 
  }
  protected function changeEnv($data = array()){
        if(count($data) > 0){

            // Read .env-file
            $env = file_get_contents(base_path() . '/.env');

            // Split string on every " " and write into array
            $env = preg_split('/\s+/', $env);;

            // Loop through given data
            foreach((array)$data as $key => $value){

                // Loop through .env-data
                foreach($env as $env_key => $env_value){

                    // Turn the value into an array and stop after the first split
                    // So it's not possible to split e.g. the App-Key by accident
                    $entry = explode("=", $env_value, 2);

                    // Check, if new key fits the actual .env-key
                    if($entry[0] == $key){
                        // If yes, overwrite it with the new one
                        $env[$env_key] = $key . "=" . $value;
                    } else {
                        // If not, keep the old one
                        $env[$env_key] = $env_value;
                    }
                }
            }

            // Turn the array back to an String
            $env = implode("\n", $env);

            // And overwrite the .env with the new data
            file_put_contents(base_path() . '/.env', $env);
            
            return true;
        } else {
            return false;
        }
    }

}
