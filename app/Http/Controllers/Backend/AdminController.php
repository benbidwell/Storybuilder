<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BackendController;
use App\Http\Controllers\AjaxController;
use App\Models\Admin;
use App\Models\Event;
use App\Models\Story;
use App\Models\User;
use Mail;
use App\Mail\UserDeleted;
use App\Mail\NewadminCreated;
use Validator;
use DB;
class AdminController extends BackendController
{
    //
  public function index(){
      $admins = Admin:: orderBy('id', 'DESC')->where('status','<',2)->get();
       return view('admin/admins/index')->with('admins',$admins);
  }
  public function create()
  {
      return view('admin/admins/add');
  }
  public function store(Request $request)
  {
      $rules = [
              'first_name'        => 'required|alpha',
              'last_name'         => 'alpha',
              'email'             => 'required|email|unique:admins,email', 
              'password'   => 'required|min:6',
              'profile_picture'   =>'mimes:jpeg,jpg,png|max:1024'          
      ];

      $messages = [
            'first_name.required'   => $this->notificationMessage('register_first_name_required','error'),
            'first_name.alpha'      => $this->notificationMessage('register_first_name_alpha','error'),      
            'last_name.alpha'       => $this->notificationMessage('register_last_name_alpha','error'),
            'email.required'        => $this->notificationMessage('register_email_required','error'),
            'email.unique'           => $this->notificationMessage('register_email_unique','error'),  
            'email.email'           => $this->notificationMessage('update_profile_email_email','error'),  
            'password.required' => $this->notificationMessage('admin_register_password_required','error'), 
            'password.min' => $this->notificationMessage('register_password_min','error'),
           'profile_picture.mimes' => $this->notificationMessage('update_profile_profile_picture_mimes','error'),
          'profile_picture.max'   => $this->notificationMessage('update_profile_profile_picture_max','error')        
      ];

        $validator = Validator::make($request->all(), $rules,$messages);

        if($validator->fails())
        {
             return Redirect::to('admin/admins/create')->withErrors($validator);               
        }
        else{ 
            $admin = new Admin();

            //store image
            if($request->profile_picture){
                $ext = $request->file('profile_picture')->getClientOriginalExtension();                     
                $profilePictureName = 'profile-pic-'.$admin->id.'-'.str_replace(' ','-',$admin->first_name).'-'.time().'.'.$ext;                    
               
                $request->file('profile_picture')->move(public_path("/profile_pictures/"), $profilePictureName);

                $admin->profile_picture = $profilePictureName;   

            }
            $admin->first_name = $request->first_name;
            $admin->last_name = $request->last_name;
            $admin->email = $request->email;
            $admin->password = Hash::make($request->password);
            $admin->save();   

            //store in user table
            $user = new user();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            if($request->profile_picture){
              $user->profile_picture = $profilePictureName;
            }
            $user->save();
            //end of store in users table
            $email = env('ADMIN_EMAIL_ID');
            $email_body = $this->emailTemplate('new_admin_created');
            $email_content = array('content' => $admin,'body'=> $email_body,'password' => $request->password);
            if($email_body){
              Mail::to($request->email)->send(new NewadminCreated($email_content,$email_body->subject)); 
            } 
            return redirect('/admin/admins')->with('success','Admin Created Successfully');  
        } 
  }


  public function show($id)
  {
     return view('admin/users/show');
  }

  public function edit($id)
  {
    $admin = Admin::find($id);
    return view('admin/admins/edit')->with('admin',$admin);
  }
  public function update(Request $request, $id)
  {
    $rules = [
        'first_name'        => 'required|alpha',
        'last_name'         => 'alpha',
        'profile_picture'   =>'mimes:jpeg,jpg,png|max:1024',
    ];

    $messages = [
        'first_name.required'   => $this->notificationMessage('update_profile_first_name_required','error'),
        'first_name.alpha'      => $this->notificationMessage('update_profile_first_name_alpha','error'),
        'last_name.alpha'       => $this->notificationMessage('update_profile_last_name_alpha','error'),
        'profile_picture.mimes' => $this->notificationMessage('update_profile_profile_picture_mimes','error'),
        'profile_picture.max'   => $this->notificationMessage('update_profile_profile_picture_max','error')
    ];

        $validator = Validator::make($request->all(), $rules,$messages);

        if($validator->fails())
        {
             return Redirect::to('admin/admins/'.$id.'/edit')->withErrors($validator);                    
        }
        else{ 
                $admin = Admin::find($id);
                if($request->profile_picture){
                    if(file_exists(public_path().'/profile_pictures/'.$admin->profile_picture)){
                        @unlink(public_path().'/profile_pictures/'.$admin->profile_picture);
                    }

                    $ext = $request->file('profile_picture')->getClientOriginalExtension();                     
                    
                    $profilePictureName = 'profile-pic-'.$admin->id.'-'.str_replace(' ','-',$admin->first_name).'-'.time().'.'.$ext;                    
                   
                    $request->file('profile_picture')->move(public_path("/profile_pictures/"), $profilePictureName);

                    $admin->profile_picture = $profilePictureName;   

                }
                $admin->first_name = $request->first_name;
                $admin->last_name = $request->last_name;
                $admin->save();  

                //changes in user table if it is admin
                $emailid = Auth::User()->email;  
                $user = User::where('email',$emailid)->first();
                if($user){
                  $user->first_name = $request->first_name;
                  $user->last_name = $request->last_name;
                  if($request->profile_picture){
                    $user->profile_picture = $profilePictureName;
                  }
                  $user->save();
                }
                //end of store in users table
                return redirect('/admin/admins/'.$id.'/edit')->with('success','Profile Updated Successfully');     
        }
  }

  public function destroy($id)
  {
      $admin = Admin::find($id);
      $admin->status = 2;
      $admin->save(); 

      //delete user if this is admin
      $emailid = Auth::User()->email;  
      $user = User::where('email',$emailid)->first();
      if($user){
        $user->status = 2;
        $user->save(); 
      }

      //send email to Admin
      $email_body = $this->emailTemplate('admin_deleted_successfully');
        $email_content = array('content' => $admin,'body'=> $email_body);
        if($email_body){
          Mail::to($admin->email)->send(new UserDeleted($email_content,$email_body->subject)); 
        }
      return redirect('/admin/admins')->with('success','Admin Deleted Successfully');     
  }
}
