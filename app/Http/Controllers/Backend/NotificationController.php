<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BackendController;
use App\Models\Notification;
use App\Models\Category;
use Validator;
class NotificationController extends BackendController
{
    public function __construct()
    {
    }

    public function index()
    {
        $notifications = Notification:: with('category')->orderBy('id', 'DESC')->get();

        return view('admin/notifications/index')->with('notifications',$notifications);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['notification'] = Notification::find($id);
        $data['categories'] = Category:: orderBy('id', 'ASC')->get();
        return view('admin/notifications/edit')->with('data',$data);
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
            'title'        => 'required|min:15',
            'description'         => 'required|min:60',
         ];

        $messages = [
            'title.max'   => $this->notificationMessage('notification_main_title_max','error'),
            'description.max'      => $this->notificationMessage('notification_description_max','error'),
        ];
        
        $validator = Validator::make($request->all(), $rules,$messages);
      
        if($validator->fails())
        {
             return Redirect::to('admin/notifications/'.$id.'/edit')->withErrors($validator);                    
        }
        else{ 
                $notifications = Notification::find($id);

                $notifications->main_title = $request->title;
                $notifications->description = $request->description;
    
                $notifications->save();   
                
              return redirect('/admin/notifications/'.$id.'/edit')->with('success','Notifications Updated Successfully');     
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
        //
    }
}
