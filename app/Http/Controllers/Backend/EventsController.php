<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\BackendController;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\Story;
use App\Models\User;
use Validator;
class EventsController extends BackendController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $events = Event:: with('user')->where('status','<',2)->orderBy('id', 'DESC')->get();
            foreach ($events as $key=>$event) {
                $events[$key]['storiesCount'] = $event->stories()->count();
            }
        return view('admin/events/index')->with('events',$events);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //return view('admin/events/add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
         return view('admin/events/show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $event = Event::findOrFail($id);

    return view('admin.events.edit')->withEvent($event);

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

        $messages = array(
            'event_title.required'=>$this->notificationMessage('event_title_required','error'),
            //'event_title.min'=>$this->notificationMessage('event_title_minchar','error'),
            'event_title.unique'=>'Event title field must contain the unique value.',
            //'event_details.min'=>$this->notificationMessage('event_details_minchar','error'),
            'event_picture.mimes'=>$this->notificationMessage('event_image_mimes','error'),
            'event_picture.max'=>$this->notificationMessage('event_image_maxsize','error'),
        );

        $validator = Validator::make($request->all(),
            array('event_title'       => 'required|unique:events,event_title,'.$id,
           // 'event_details'      => 'min:100',
            'event_picture'  => 'mimes:jpeg,jpg,png|max:4096',
        ));

        if($validator->fails())
        {
           //return $this->setResponseFormat(400,NULL,$validator->messages());    
           return Redirect::to('admin/events/'.$id.'/edit/')->withErrors($validator);              
        }
        else{                     
            $event = Event::find($id);                  
            
            if($request->event_picture){

                $ext = $request->file('event_picture')->getClientOriginalExtension();                     
                    
                $filename = 'event-pic'.'-'.time().'.'.$ext;                    
                 
                $request->file('event_picture')->move(public_path("/event_pictures/"), $filename);
               
                $event->event_picture = $filename;
            }

            $event->event_title = $request->event_title;

            $event->event_details = $request->event_details;

            $event->save();
            return redirect('/admin/events/'.$id.'/edit/')->with('success','Event Updated Successfully');      
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
       return redirect('/admin/events')->with('success','Event Deleted Successfully'); 
    }
}
