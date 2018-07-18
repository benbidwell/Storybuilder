@extends('layouts.admin')
<!-- BEGIN THEME PANEL -->
@section('content')
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="{{url('admin')}}">Home</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <a href="{{url('admin/users')}}">Users</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>Add User</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title">
    <small></small>
</h1>
<!-- END PAGE TITLE-->
<div class="row">
    <div class="col-md-12 ">
        <!-- BEGIN SAMPLE FORM PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-red-sunglo">
                    <i class="icon-settings font-red-sunglo"></i>
                    <span class="caption-subject bold uppercase"> Add User</span>
                </div>
            </div>
           @if ($message = Session::get('success'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                    <strong>{{ $message }}</strong>
            </div>
            @endif
<section class="wrapper">
  <!-- <h3><i class="fa fa-angle-right"></i> Users</h3> -->
    <div class="alert alert-danger" id="imageerrorwrapper" style="display:none;">
            <p id="imageerror"></p>
    </div>
  @if($errors->any())
    <div class="alert alert-danger">
        @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif
    <div class="portlet-body form">
         <form action="{{route('admin.users.store')}}" method="post" enctype="multipart/form-data">
         {{ csrf_field() }}
            <div class="form-body">
                <div class="form-group">
                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                                <img src="http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image" alt="" /> 
                                </div>
                            <div class="fileinput-preview fileinput-exists thumbnail"  style="max-width: 200px; max-height: 150px;"> </div>
                            <div>
                                <span class="btn default btn-file">
                                    <span class="fileinput-new"> Select image </span>
                                    <span class="fileinput-exists"> Change </span>
                                    <input type="file" id="uploadedimage" name="profile_picture"> </span>
                                <a href="javascript:;" class="btn default fileinput-exists" data-dismiss="fileinput"> Remove </a>
                            </div>
                        </div>
                        <div class="clearfix margin-top-10">
                            <span class="label label-danger"></span>
                        </div>
                    </div>
                <div class="form-group">
                    <label class="control-label">First Name</label>
                    <div class="input-icon right">
                        <input type="text" name="first_name" class="form-control @if($errors->has('first_name')) error @endif"> </div>
                </div>

                <div class="form-group">
                    <label class="control-label">Last Name</label>
                    <div class="input-icon right">
                        <input type="text" name="last_name" class="form-control @if($errors->has('last_name')) error @endif"> </div>
                </div>
                <div class="form-group">
                    <label class="control-label">Country</label>
                    <div class="input-icon right">
                        <select name="country" class="form-control"> 
                        @foreach($countries as $country)
                            <option value="{{$country->id}}">{{$country->country_name}} ({{$country->country_code}})</option>
                        @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label">Email</label>
                    <div class="input-icon right">
                        <input type="email" name="email" class="form-control @if($errors->has('email')) error @endif"> </div>
                </div>

                <div class="form-group">
                    <label class="control-label">Password</label>
                    <div class="input-icon right">
                        <input type="password" name="password" class="form-control @if($errors->has('password')) error @endif"> </div>
                </div>
    
            <div class="form-actions noborder">
                <button type="submit" class="btn blue">Submit</button>
                <button type="reset" class="btn default">Cancel</button>
            </div>
        </form>
    </div>
</div>
<!-- END SAMPLE FORM PORTLET-->
@stop