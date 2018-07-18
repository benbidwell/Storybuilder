<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\BackendController;
use App\Http\Controllers\AjaxController;
use App\Models\EmailTemplate;
use App\Models\Category;
use Validator;

class EmailController extends BackendController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth:web');
    }

    public function index()
    {
        $emailtemplates = EmailTemplate:: with('category')->orderBy('id', 'DESC')->get();    
        return view('admin/emailtemplates/index')->with('emailtemplates',$emailtemplates);
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
        $data['emailtemplate'] = EmailTemplate::find($id);
        $data['categories'] = Category:: orderBy('id', 'ASC')->get();
        return view('admin/emailtemplates/edit')->with('data',$data);
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
        $emailtemplate = EmailTemplate::find($id);

        $emailtemplate->title = $request->title;
        $emailtemplate->subject = $request->subject;
        $emailtemplate->description = $request->description;
        $emailtemplate->save();   
        
      return redirect('/admin/emailtemplates/'.$id.'/edit')->with('success','Email Template Updated Successfully');     
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
