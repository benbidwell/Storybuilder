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
            <span>Change Profile</span>
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
                    <span class="caption-subject bold uppercase"> Change Profile</span>
                </div>
            </div>
            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button> 
                        <strong>{{ $message }}</strong>
                </div>
            @endif
            <section class="wrapper">
             <!--  <h3><i class="fa fa-angle-right"></i> Users</h3> -->
                @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
                 @endif
                <div class="portlet-body form">
                    <form action="{{@url('admin/edit-profile')}}" method="post" enctype="multipart/form-data">
                     {{ csrf_field() }}
                     {{ method_field('PATCH') }}
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label">First Name</label>
                            <div class="input-icon right">
                           <input type="password" value="" class="form-control" name="old_password"/> </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Last Name</label>
                        <div class="input-icon right">
                        <input type="text"  value="" name="new_password" class="form-control"> 
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Email</label>
                        <div class="input-icon right">
                        <input type="text" value="" name="email" class="form-control"> 
                        </div>
                    </div>
                </div>
            <div class="form-actions noborder">
                <button type="submit" class="btn blue">Update Profile</button>
                <button type="button" class="btn default">Cancel</button>
            </div>
        </form>
    </div>
</div>
<!-- END SAMPLE FORM PORTLET-->
@stop