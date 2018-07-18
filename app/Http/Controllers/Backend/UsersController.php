<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BackendController;
use App\Http\Controllers\AjaxController;
use App\Models\User;
use App\Models\Admin;
use App\Models\Country;
use Mail;
use App\Mail\AdminUserCreated;
use App\Mail\AdminAuthorizationApproved;
use App\Mail\UserStatusChanged;
use App\Mail\UserDeleted;
use Validator;
class UsersController extends BackendController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function __construct()
    // {
    //     $this->middleware('auth:web');
    // }

    public function index()
    {
      $users = Admin::join('users', function($join)
        {
            $join->on('admins.email', '=', 'users.email');
        })->get(['users.id']);
      
      $notin = array();
        
        foreach($users as $user){
          $notin[] = $user->id;
        }

       $users = User::with('country')->orderBy('users.id', 'DESC')->where('users.status','<',2)->whereNotIn('id',$notin)->get();
        return view('admin/users/index')->with('users',$users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = Country:: orderBy('id', 'ASC')->get();
        return view('admin/users/add')->with('countries',$countries);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $rules = [
                    'first_name'        => 'required|alpha|min:3|different:last_name',
                    'last_name'         => 'required|alpha|min:3|different:first_name',
                    'email'             => 'required|email|unique:users,email', 
                    'password'   => 'required|min:6',
                    'profile_picture'   => 'mimes:jpeg,jpg,png|max:1024'             
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
             return Redirect::to('admin/users/create')->withErrors($validator);               
        }
        else{ 
            $user = new User();

            //store image
            if($request->profile_picture){
                $ext = $request->file('profile_picture')->getClientOriginalExtension();                     
                
                $profilePictureName = 'profile-pic-'.$user->id.'-'.str_replace(' ','-',$user->first_name).'-'.time().'.'.$ext;                    
               
                $request->file('profile_picture')->move(public_path("/profile_pictures/"), $profilePictureName);

                $user->profile_picture = $profilePictureName;   

            }
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->country_id = $request->country;
            $user->password = Hash::make($request->password);

            $user->save();   

            $email = env('ADMIN_EMAIL_ID');
            $email_body = $this->emailTemplate('user_created_by_admin');
            $email_content = array('content' => $user,'body'=> $email_body,'password' => $request->password);
            if($email_body){
              Mail::to($request->email)->send(new AdminUserCreated($email_content,$email_body->subject));  
            }
            return redirect('/admin/users')->with('success','User Created Successfully');  
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       return view('admin/users/show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['user'] = User::find($id);
        $data['countries'] = Country:: orderBy('id', 'ASC')->get();
        return view('admin/users/edit')->with('data',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
         $rules = [
                    'first_name'        => 'required|alpha|min:3|different:last_name',
                    'last_name'         => 'required|alpha|min:|different:first_name',
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
                    'profile_picture.mimes' => $this->notificationMessage('update_profile_profile_picture_mimes','error'),
                    'profile_picture.max'   => $this->notificationMessage('update_profile_profile_picture_max','error')    
                ];

        $validator = Validator::make($request->all(), $rules,$messages);

        if($validator->fails())
        {
             return Redirect::to('admin/users/'.$id.'/edit')->withErrors($validator);                    
        }
        else{ 
                $user = User::find($id);

                if($request->profile_picture){
                    if(file_exists(public_path().'/profile_pictures/'.$user->profile_picture)){
                        @unlink(public_path().'/profile_pictures/'.$user->profile_picture);
                    }

                    $ext = $request->file('profile_picture')->getClientOriginalExtension();                     
                    
                    $profilePictureName = 'profile-pic-'.$user->id.'-'.str_replace(' ','-',$user->first_name).'-'.time().'.'.$ext;                    
                   
                    $request->file('profile_picture')->move(public_path("/profile_pictures/"), $profilePictureName);

                    $user->profile_picture = $profilePictureName;   

                }
                if($request->activity_header_logo){
                   $user->activity_header_logo = 1;
                }
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->country_id = $request->country;
                $user->save();   
              return redirect('/admin/users/'.$id.'/edit')->with('success','User Updated Successfully');     
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        $user = User::find($id);
        $user->status = 2;
        $user->save(); 

        //send mail to User
        $email_body = $this->emailTemplate('user_deleted_successfully');
        $email_content = array('content' => $user,'body'=> $email_body);
        if($email_body){
          Mail::to($user->email)->send(new UserDeleted($email_content,$email_body->subject)); 
        }
        return redirect('/admin/users')->with('success','User Deleted Successfully');     
    }
    public function userauthorized(Request $request){
        if($request->ajax())
        {
         
            $data = array();
              if($request->userid){
                  $user =new User;
                  $user = User::find($request->userid);
                  if($user->authorized==0)
                  {
                        $user->authorized=1;
                        $email_body = $this->emailTemplate('user_authorization_approved');
                        $email_content = array('content' => $user,'body'=> $email_body);
                        if($email_body){
                          Mail::to($user->email)->send(new AdminAuthorizationApproved($email_content,$email_body->subject)); 
                        }
                  }
                  else
                  {
                     
                      $user->authorized=0;
                  }
                  $user->save();
                  $msg='Authorization Status changed for '.$user->first_name.'';
                  return response()->json($msg);
              }  
        }
    }

    //change status of user
    public function userStatusChange(Request $request){
        if($request->ajax())
        {
            $data = array();
              if($request->userid){
                $user =new User;
                $user = User::find($request->userid);
                if($user->status==0)
                {
                    $user->status=1;
                    $email_body = $this->emailTemplate('user_status_to_inactive');
                    $email_content = array('content' => $user,'body'=> $email_body); 
                }
                else
                {
                    $user->status=0;
                    $email_body = $this->emailTemplate('user_status_to_active');
                    $email_content = array('content' => $user,'body'=> $email_body);
                } 
                $user->save();
                if($email_body){
                  Mail::to($user->email)->send(new UserStatusChanged($email_content,$email_body->subject)); 
                }
                $msg='Status changed for '.$user->first_name.'';
                  return response()->json($msg);
              }  
        }
    }
}