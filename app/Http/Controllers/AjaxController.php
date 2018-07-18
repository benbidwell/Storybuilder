<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Admin;
use App\Models\EmailTemplate;
use App\Models\Event;
use App\Models\Story;
use App\Models\Notification;
use Mail;
use App\Mail\AdminStatusChanged;
class AjaxController extends Controller
{

    public function index(Request $request)
    {
      
    }

	public function postindex(Request $request)
    {
        if($request->ajax())
        {
            if($request->action)
            {
              $action = $request->action;
              switch ($action) {
            		case 'changestatusofuser':
                  $data = array();
                  if($request->userid){
                      $user =new User;
                      $user = User::find($request->userid);
                      if($user->status==0)
                      {
                          $user->status=1;
                      }
                      else
                      {
                          $user->status=0;
                      }
                      $user->save();
                      $msg='Status changed for '.$user->first_name.'';
                      return response()->json($msg);
                  }         
                exit;
                break;

                case 'changestatusofadmin':
                  $data = array();
                  if($request->userid){
                      $admin =new Admin;
                      $admin = Admin::find($request->userid);
                      //change status for user if this is admin
                      $emailid = Auth::User()->email;  
                      $user = User::where('email',$emailid)->first();
                      if($admin->status==0)
                      {
                        $status = 1; 
                        $email_body = app('App\Http\Controllers\BackendController')->emailTemplate('admin_status_to_inactive');
                         $email_content = array('content' => $admin,'body'=> $email_body);
                      }
                      else
                      {
                        $status = 0;
                        $email_body = app('App\Http\Controllers\BackendController')->emailTemplate('admin_status_to_active');
                        $email_content = array('content' => $admin,'body'=> $email_body);
                      }

                      $admin->status = $status;
                      $admin->save();

                      if($user){
                        $user->status = $status;
                        $user->save();
                      }
                      
                      //end of cahnging status if this is admin
                      if($email_body){
                       Mail::to($admin->email)->send(new AdminStatusChanged($email_content,$email_body->subject)); 
                      }
                      $msg='Status changed for '.$admin->first_name.'';
                      return response()->json($msg);
                  }         
                exit;
                break;

                case 'changestatusofemail':
                  $data = array();
                  if($request->emailid){
                      $email = new EmailTemplate;
                      $email = EmailTemplate::find($request->emailid);
                      if($email->status==0)
                      {
                          $email->status=1;
                      }
                      else
                      {
                          $email->status=0;
                      }
                      $email->save();
                      $msg='Status changed for Email "'.$email->title.'"';
                      return response()->json($msg);
                  }         
                exit;
                break;

                case 'changestatusofevent':
                  $data = array();
                  if($request->eventid){
                      $event = new Event;
                      $event = Event::find($request->eventid);
                      if($event->status==0)
                      {
                          $event->status=1;
                      }
                      else
                      {
                          $event->status=0;
                      }
                      $event->save();
                      $msg='Status changed for Event "'.$event->event_title.'"';
                      return response()->json($msg);
                  }         
                exit;
                break;

                case 'changestatusofstory':
                  $data = array();
                  if($request->storyid){
                      $story = new Story;
                      $story = Story::find($request->storyid);
                      if($story->status==0)
                      {
                          $story->status=1;
                      }
                      else
                      {
                          $story->status=0;
                      }
                      $story->save();
                      $msg='Status changed for Story "'.$story->story_title.'"';
                      return response()->json($msg);
                  }         
                exit;
                break;

                case 'changestatusofnotification':
                  $data = array();
                  if($request->notificationid){
                      $notification = new Notification;
                      $notification = Notification::find($request->notificationid);
                      if($notification->status==0)
                      {
                          $notification->status=1;
                      }
                      else
                      {
                          $notification->status=0;
                      }
                      $notification->save();
                      $msg='Status changed for Notification "'.$notification->main_title.'"';
                      return response()->json($msg);
                  }         
                exit;
                break;
                case 'ChangeUserProfilePicture':
                  $data = array();
                  if($request->notificationid){
                    $validator = Validator::make($request->all(), [
                        'title' => 'required',
                        'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                      ]);
                      if ($validator->passes()) {
                        $input = $request->all();
                        $input['image'] = time().'.'.$request->image->getClientOriginalExtension();
                        $request->image->move(public_path('images'), $input['image']);
                        AjaxImage::create($input);
                        return response()->json(['success'=>'done']);
                      }
                      return response()->json(['error'=>$validator->errors()->all()]);
                  }         
                exit;
                break;
                default:
                exit;
            }
        }
    }
}
}
