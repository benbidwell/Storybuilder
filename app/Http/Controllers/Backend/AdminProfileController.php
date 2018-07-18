<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BackendController;
use App\Models\Admin;
use App\Models\User;
use Validator;
class AdminProfileController extends BackendController
{
    public function __construct(){

    }

    public function index(){
      // $data = array();
      $userID = auth()->user()->id;
      // echo $userID;
      $admin = Admin::find($userID);
    return view('admin/profile/profile')->with('admin',$admin);
    }
    public function change_password_form(){
      $userID = auth()->user()->id;
      $admin = Admin::find($userID);
      return view('admin/profile/change_password')->with('admin',$admin); 
    }
    public function editProfile(Request $request){
        $rules = [
        'first_name'        => 'required|alpha',
        'last_name'         => 'alpha',
    ];

    $messages = [
        'first_name.required'   => $this->notificationMessage('update_profile_first_name_required','error'),
        'first_name.alpha'      => $this->notificationMessage('update_profile_first_name_alpha','error'),
        'last_name.alpha'       => $this->notificationMessage('update_profile_last_name_alpha','error'),
    ];

        $validator = Validator::make($request->all(), $rules,$messages);

        if($validator->fails())
        {
            return Redirect::to('admin/profile')->withErrors($validator); 
        }
        else{ 
                $admin_id = Auth::User()->id;   
                $admin = Admin::find($admin_id);
                $admin->first_name = $request->first_name;
                $admin->last_name = $request->last_name;
                $admin->email = $request->email;
                $admin->save();   
                return redirect('/admin/profile')->with('success','Admin Updated Successfully');     
        }
    }

    //change password
    public function changePassword(Request $request){
    
          $rules = [
            'old_password'      => 'required|min:6',
            'new_password'      => 'required|min:6|different:old_password',
            'confirm_password'  => 'required|min:6|same:new_password'
          ];

          $messages = [                                   
            'old_password.required'         => 'You cant leave old password field empty',         
            'old_password.min'              => 'Old Password must be 6 characters long',               
            'new_password.required'         => 'You cant leave new password field empty',         
            'new_password.min'              => 'New password must be 6 characters long',   
            'new_password.different'        => 'New password must be different from from old password',
            'confirm_password.required'     => 'You cant leave confirm password field empty',         
            'confirm_password.min'          => 'Confirm password must be 6 characters long',   
            'confirm_password.same'         => 'Confirm password must be same as the new password'                                      
          ];


          $this->validate($request, $rules,$messages);
          $current_password = Auth::User()->password; 
          
          if(Hash::check($request['old_password'], $current_password)){
            $hashedpassword = Hash::make($request['new_password']);
        
            $admin_id = Auth::User()->id;                       
            $admin = Admin::find($admin_id);
            $admin->password = $hashedpassword;
            $admin->save();  
             
             //change password in user table
            $emailid = Auth::User()->email;  
            $user = User::where('email',$emailid)->first();
            if($user){
              $user->password = $hashedpassword;
              $user->save();
            }
             return redirect('/admin/change-password')->with('success','Password changed successfully'); 
          }else{
           
             return redirect('/admin/change-password')->with('error','Old Password is not correct');
          }
    }
}
