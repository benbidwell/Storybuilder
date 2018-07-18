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
            <a href="{{url('admin/admins')}}">Admins</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>Edit User</span>
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
                    <span class="caption-subject bold uppercase"> Edit Admin</span>
                </div>
            </div>
           @if ($message = Session::get('success'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button> 
                    <strong>{{ $message }}</strong>
            </div>
            @endif
<section class="wrapper">
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
         <form action="{{@url('admin/admins/'.$admin->id)}}" method="post" enctype="multipart/form-data">
         {{ csrf_field() }}
         {{ method_field('PATCH') }}
            <div class="form-body">
                <div class="form-group">
                <div class="fileinput fileinput-new" data-provides="fileinput">
                    <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                        @if($admin->profile_picture == '') 
                        <img src="http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image" alt="" /> @else
                         <img src="{{ asset('profile_pictures')}}/{{$admin->profile_picture}}" alt="" /> 
                        @endif </div>
                    <div class="fileinput-preview fileinput-exists thumbnail"  style="max-width: 200px; max-height: 150px;"> </div>
                    <div>
                        <span class="btn default btn-file">
                            <span class="fileinput-new"> Select image </span>
                            <span class="fileinput-exists"> Change </span>
                            <input type="file" name="profile_picture" id="uploadedimage"> </span>
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
                        <input type="text" value="{{$admin->first_name}}" name="first_name" class="form-control @if($errors->has('first_name')) error @endif"> </div>
                </div>
                <div class="form-group">
                    <label class="control-label">Last Name</label>
                    <div class="input-icon right">
                        <input type="text" value="{{$admin->last_name}}" name="last_name" class="form-control @if($errors->has('last_name')) error @endif"> </div>
                </div>
                <div class="form-group">
                    <label class="control-label">Last Name</label>
                    <div class="input-icon right">
                        <input type="email" value="{{$admin->email}}" name="email" disabled class="form-control"> </div>
                </div>
            </div>
            <div class="form-actions noborder">
                <button type="submit" class="btn blue">Submit</button>
                <button type="button" class="btn default">Cancel</button>
            </div>
        </form>
    </div>
</div>
<!-- END SAMPLE FORM PORTLET-->
@stop