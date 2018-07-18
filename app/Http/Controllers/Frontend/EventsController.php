<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\FrontendController;
use JWTAuth;
use App\Models\Event;
use App\Models\Story;
use App\Models\User;
use Response;
use Validator;
use Carbon\Carbon;
use Mail;
use App\Mail\EventCreated;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class EventsController extends FrontendController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //echo JWTAuth::getToken();
        //die();
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
            }else{
                    $events = Event:: orderBy('id', 'ASC')->where('status',0)->where('user_id',$user->id)->get();

                        foreach ($events as $key=>$event) {
                            $events[$key]['storiesCount'] = $event->stories()->where('status',0)->count();
                        }
                    $events = $events->chunk(4);
                    $events->toArray();
                    return $this->setResponseFormat(200, 'All Events',array('events' =>$events), NULL, NULL, 0);
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

    public function allevents($limit = NULL,$offset= NULL){
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
            }else{

                    $events = Event:: orderBy('id', 'DESC')->where('status',0)->where('user_id',$user->id)->limit(@$limit)->offset(@$offset)->get();
                        foreach ($events as $key=>$event) {
                            $events[$key]['storiesCount'] = $event->stories()->where('status',0)->where('publish_status',1)->count();
                        }
                    // $events = $events->chunk(4);
                    // $events->toArray();
                    return $this->setResponseFormat(200, 'All Events',array('events' =>$events), NULL, NULL, 0);
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
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
            }else{

               // if(!isset($request->event_details) || empty($request->event_details)){
                    $rules = [
                        'event_title' => 'required|unique:events,event_title',
                        'event_picture'  => 'mimes:jpeg,jpg,png|max:4096'
                    ];

                    $messages = array(
                    'event_title.required'=>$this->notificationMessage('event_title_required','error'),
                    'event_title.unique'=>$this->notificationMessage('unique_event_title','error'),
                    'event_picture.mimes'=>$this->notificationMessage('event_image_mimes','error'),
                    'event_picture.max'=>$this->notificationMessage('event_image_maxsize','error')
                    );


                /*}else{

                     $rules = [
                        'event_title'    => 'required|min:15|unique:events,event_title',
                        'event_details'  => 'min:100',
                        'event_picture'  => 'mimes:jpeg,jpg,png|max:4096'
                    ];

                    $messages = array(
                        'event_title.required'=>$this->notificationMessage('event_title_required','error'),
                        'event_title.min'=>$this->notificationMessage('event_title_minchar','error'),
                        'event_title.unique'=>$this->notificationMessage('unique_event_title','error'),
                        'event_details.min'=>$this->notificationMessage('event_details_minchar','error'),
                        'event_picture.mimes'=>$this->notificationMessage('event_image_mimes','error'),
                        'event_picture.max'=>$this->notificationMessage('event_image_maxsize','error')
                     );

                } */

                $validator = Validator::make($request->all(),$rules,$messages);

                if($validator->fails())
                {
                    return $this->setResponseFormat(400,NULL,$validator->messages());
                }
                else{
                    $filename = '';
                    if($request->event_picture){
                        $ext = $request->file('event_picture')->getClientOriginalExtension();
                        $filename = 'event-pic'.'-'.time().'.'.$ext;
                        $request->file('event_picture')->move(public_path("/event_pictures/"), $filename);
                    }

                    $event = new Event();
                    $event->event_title = $request->event_title;
                    $event->event_details = $request->event_details;
                    $event->event_picture = $filename;
                    $event->user_id = $user->id;
                    $event->status = 0;
                    $event->save();
                    $carbon = Carbon::now();

                    $event->created_at = $carbon->format('Y-m-d');
                    // Email for User
                     $email = $user->email;
                    $email_body = $this->emailTemplate('event_created');
                    $email_content = array('content' => $event,'body'=> $email_body,'userdata' => $user);
                    if($email_body){
                    Mail::to($email)->send(new EventCreated($email_content,$email_body->subject));
                    }

                    // Email for Admin

                    $email = 'developer.walkwel@gmail.com';
                    $user_email_body = $this->emailTemplate('event_created_for_admin');
                    $user_email_content = array('content' => $event,'body' => $user_email_body,'userdata' => $user);
                    if($email_body){
                        Mail::to($email)->send(new EventCreated($user_email_content,$user_email_body->subject));
                    }
                    return $this->setResponseFormat(200, $this->notificationMessage('event_created_successful','success'),array('event' =>$event));
                    //return $this->setResponseFormat(200, $this->notificationMessage('event_created_successful','success'));
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
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
            }else{

                	$event = Event::where('status',0)->where('user_id',$user->id)->find($id);
                	if(!empty($event)){
                        $event['storiesCount'] = $event->stories()->where('status',0)->where('publish_status',1)->count();
                		return $this->setResponseFormat(200, $this->notificationMessage('single_event','success'), $event);
                	}
                	else{
                		return $this->setResponseFormat(400, $this->notificationMessage('event_not_found','error'),NULL,NULL,0);
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        // print_r($request->all());
        // die();
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
            }else{
               // if(!isset($request->event_details) || empty($request->event_details) || $request->event_details == '' || $request->event_details == NULL || $request->event_details =='null'){

                    $rules = [
                        'event_title'    => 'required|unique:events,event_title,'.$id,
                        'event_picture'  => 'mimes:jpeg,jpg,png|max:4096'
                    ];

                    $messages = array(
                        'event_title.required'=>$this->notificationMessage('event_title_required','error'),
                        'event_title.min'=>$this->notificationMessage('event_title_minchar','error'),
                        'event_title.unique'=>'Event title field must contain the unique value.',
                        'event_picture.mimes'=>$this->notificationMessage('event_image_mimes','error'),
                        'event_picture.max'=>$this->notificationMessage('event_image_maxsize','error')
                    );


               /* }else{

                     $rules = [
                        'event_title'    => 'required|min:15|unique:events,event_title,'.$id,
                        'event_details'  => 'min:100',
                        'event_picture'  => 'mimes:jpeg,jpg,png|max:4096'
                    ];

                    $messages = array(
                        'event_title.required'=>$this->notificationMessage('event_title_required','error'),
                        'event_title.min'=>$this->notificationMessage('event_title_minchar','error'),
                        'event_title.unique'=>'Event title field must contain the unique value.',
                        'event_details.min'=>$this->notificationMessage('event_details_minchar','error'),
                        'event_picture.mimes'=>$this->notificationMessage('event_image_mimes','error'),
                        'event_picture.max'=>$this->notificationMessage('event_image_maxsize','error')
                     );

                } */

                $validator = Validator::make($request->all(),$rules,$messages);

                if($validator->fails())
                {
                   return $this->setResponseFormat(400,NULL,$validator->messages());
                }
                else{

                    $event = Event::where('user_id',$user->id)->find($id);

                    $event->event_title = $request->event_title;
                    if($request->event_details){
                        $event->event_details = $request->event_details;
                    }
                    else{
                        $event->event_details = '';
                    }

                    if($request->event_picture){

                       if(file_exists(public_path().'/event_picture/'.$event->event_picture)){
                           @unlink(public_path().'/event_pictures/'.$event->event_picture);
                       }

                       $ext = $request->file('event_picture')->getClientOriginalExtension();
                       $filename = 'event-pic'.'-'.time().'.'.$ext;
                       $request->file('event_picture')->move(public_path("/event_pictures/"), $filename);
                       $event->event_picture = $filename;
                    }

                        $event->save();

                	//return $this->setResponseFormat(200,$this->notificationMessage('event_updated_successfully','success'));
                    return $this->setResponseFormat(200, $this->notificationMessage('event_updated_successfully','success'),array('details' =>$request->event_details));


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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
            }else{
                $event = Event::find($id);
                $event->status = 2;
                $event->save();
                //disable all stories of the event
                $stories = Story::where('event_id', '=', $id)->get();
                foreach($stories as $story){
                    $story = Story::find($story->id);
                     $story->status = 2;
                    $story->save();
                }
               return $this->setResponseFormat(200,$this->notificationMessage('event_deleted_successfully','success'));
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

    //search event function
    public function serachEvent($search = NULL)
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
            }else{
                if($search == ''){
                    $returnFormat = $this->setResponseFormat(400, 'Please enter something to search',NULL, NULL, NULL, 0);
                    return $returnFormat;
                }

                   $events = Event::where('status',0)->where('user_id',$user->id)->where(function($query) use ($search)
                    {
                        $query->orWhere('event_title', 'LIKE', '%'.$search.'%')
                            ->orWhere('event_details', 'LIKE', '%'.$search.'%');
                    })->orderBy('id', 'DESC')->get();

                    foreach ($events as $key=>$event) {
                            $events[$key]['storiesCount'] = $event->stories()->where('status',0)->count();
                        }
                                    // foreach ($events as $key=>$event) {
                   //     $events[$key]['stories']= $event->stories()->Where('story_title', 'LIKE', '%'.$search.'%')->orWhere('story_details', 'LIKE', '%'.$search.'%')->get();
                   // }

                    $count = $events->count();
                    if($count != 0){
                        return $this->setResponseFormat(200, 'Your Search Results',array('events' =>$events), NULL, NULL, 0);

                    }else{
                       return $this->setResponseFormat(404, $this->notificationMessage('event_search_notfound','error'),NULL, NULL, 0);
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

    //update events picture

    public function updateEventPicture(Request $request, $id){
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
            }else{
                    $updateEvent = Event::find($id);
                        if(file_exists(public_path().'/event_picture/'.$updateEvent->event_picture)){
                            @unlink(public_path().'/event_pictures/'.$updateEvent->event_picture);
                        }
                    $updateEvent->event_picture = '';
                    $updateEvent->save();

                    return $this->setResponseFormat(200, $this->notificationMessage('event_pic_removed','success'));
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

    //search event function
    public function AutoloadSearching($search = NULL)
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
            }else{
                    $events = Event::where('status',0)->where('user_id',$user->id)->orderBy('id', 'DESC')->get();

                    return $this->setResponseFormat(200, 'Your Search Results',array('events' =>$events), NULL, NULL, 0);

                    // }else{
                    //    return $this->setResponseFormat(404, $this->notificationMessage('event_search_notfound','error'),NULL, NULL, 0);
                    // }
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
    public function delete_event_confirmation(){
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->setResponseFormat(400, $this->notificationMessage('user_not_exists','error'));
            }else{
                  return $this->setResponseFormat(200, $this->notificationMessage('delete_event_confirmation','confirmation'));
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

    public function event_analysis(Request $request){
        $youtube_arr=[];
        $facebook_arr=[];
        $twitter_arr=[];
        if($request->has('id')){
            $event = Event::find($request->input('id'));
         
            foreach($event->publishedStories as $story){
              
                if(@$story->facebook)
                        array_push($facebook_arr,@$story->facebook);
                
                if(@$story->youtube)
                    array_push($youtube_arr,@$story->youtube);

                if(@$story->twitter)
                    array_push($twitter_arr,@$story->twitter);
            }
            return $this->setResponseFormat(200, 'Your Search Results',array('youtube_ids' =>$youtube_arr, 'facebook_ids' => $facebook_arr, 'twitter_ids'=> $twitter_arr), NULL, NULL, 0);
           
        }
        
    }
}
