@extends('layouts.admin')
<!-- BEGIN THEME PANEL -->
@section('content')
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="{{url('admin')}}">Admin</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>Settings</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title">Settings
    <small></small>
</h1>
<!-- END PAGE TITLE-->
@if ($message = Session::get('success'))
<div class="alert alert-success alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button> 
        <strong>{{ $message }}</strong>
</div>
@endif
  @if($errors->any())
    <div class="alert alert-danger">
        @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif  
 <form action="{{@url('admin/settings/edit/'.$adminsetting[0]->id)}}" method="post">
    {{ csrf_field() }}
<div class="row">
    <div class="col-md-12 ">
        <!-- BEGIN SAMPLE FORM PORTLET-->
        <div class="portlet light bordered">
           

<section class="wrapper">
    <div class="portlet-body form">
    <div class="portlet-title">
        <div class="caption font-red-sunglo">
            <i class="icon-settings font-red-sunglo"></i>
            <span class="caption-subject bold uppercase"> SMTP Details</span>
        </div>
    </div>
    <div class="form-body">
        <div class="form-group">
            <label class="control-label">Port</label>
            <div class="input-icon right">
                <input type="text" name="smtp_port" value="{{$adminsetting[0]->smtp_port}}" class="form-control"> </div>
        </div>

        <div class="form-group">
            <label class="control-label">Host</label>
            <div class="input-icon right">
                <input type="text" value="{{$adminsetting[0]->smtp_host}}" name="smtp_host" class="form-control"> </div>
        </div>

        <div class="form-group">
            <label class="control-label">Username</label>
            <div class="input-icon right">
                <input type="text" name="smtp_username" value="{{$adminsetting[0]->smtp_username}}" class="form-control"> </div>
        </div>

        <div class="form-group">
            <label class="control-label">Password</label>
            <div class="input-icon right">
                <input type="password" value="{{$adminsetting[0]->smtp_password}}" name="smtp_password" class="form-control"> </div>
        </div>
    </div>
    <!-- google analytics settings -->
    <div class="portlet-title">
        <div class="caption font-red-sunglo">
            <i class="icon-settings font-red-sunglo"></i>
            <span class="caption-subject bold uppercase"> Google Analytics</span>
        </div>
    </div>
    <div class="form-body">
        <div class="form-group">
            <label class="control-label">Google Analytics Code</label>
            <div class="input-icon right">
                <textarea class="form-control" name="google_analytics_code" rows="3">{{$adminsetting[0]->google_analytics_code}}</textarea>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label">Pundit Title Text</label>
            <div class="input-icon right">
                <input type="text" value="{{$adminsetting[0]->pundit_title_text}}" name="pundit_title_text" class="form-control"> </div>
        </div>
    </div>

    <!-- facebook settings -->
    <div class="portlet-title">
        <div class="caption font-red-sunglo">
            <i class="icon-settings font-red-sunglo"></i>
            <span class="caption-subject bold uppercase"> Facebook Pixle</span>
        </div>
    </div>
    <div class="form-body">
        <div class="form-group">
            <label class="control-label">Facebook Pixel Code</label>
            <div class="input-icon right">
                <textarea class="form-control" name="facebook_pixel_code" rows="3">{{$adminsetting[0]->facebook_pixel_code}}</textarea>
            </div>
        </div>
    </div>
    <!-- Transition settings -->
    <div class="portlet-title">
        <div class="caption font-red-sunglo">
            <i class="icon-settings font-red-sunglo"></i>
            <span class="caption-subject bold uppercase"> Transition Time</span>
        </div>
    </div>
    <div class="form-body">
        <div class="form-group">
            <label class="control-label">Transition Time</label>
            <div class="input-icon right">
                <input type="number" value="{{$adminsetting[0]->trasition_time}}" name="trasition_time" class="form-control"> </div>
        </div>
    </div>
    <div class="form-actions noborder">
        <button type="submit" class="btn blue">Submit</button>
        <button type="reset" class="btn default">Cancel</button>
    </div> 
</form>
</div>
<!-- END SAMPLE FORM PORTLET-->
@stop