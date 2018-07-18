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
            <a href="{{url('admin/notifications')}}">Notifications</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>Edit Notifications</span>
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
                    <span class="caption-subject bold uppercase"> Edit Notification</span>
                </div>
            </div>
            <section class="wrapper">
            
              @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
            <div class="portlet-body form">
                 <form action="{{@url('admin/notifications/'.$data['notification']->id)}}" method="post" enctype="multipart/form-data">
                 {{ csrf_field() }}
                 {{ method_field('PATCH') }}
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label">Title</label>
                            <div class="input-icon right">
                                <input type="text" value="{{$data['notification']->main_title}}" name="title" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="description" rows="3">{{$data['notification']->description}}</textarea>
                        </div>
                        <!-- <div class="form-group"> 
                            <label for="single-prepend-text" class="control-label">Category</label>
                            <div class="input-group select2-bootstrap-prepend">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button" data-select2-open="single-prepend-text">
                                        <span class="glyphicon glyphicon-search"></span>
                                    </button>
                                </span>
                                <select id="single-prepend-text" class="form-control select2" name="category" disabled>
                                   @foreach($data['categories'] as $category)
                                    <option value="{{$category->id}}" {{ $category->id == $data['notification']->category_id ? 'selected' : '' }}
                                    >  
                                    {{$category->category_name}} </option>
                                @endforeach
                                </select>
                            </div>
                        </div> -->
  
                        <div class="form-group">
                            <label for="single-prepend-text" class="control-label">Type</label>
                            <div class="input-group select2-bootstrap-prepend">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" type="button" data-select2-open="single-prepend-text">
                                        <span class="glyphicon glyphicon-search"></span>
                                    </button>
                                </span>
                                <select id="single-prepend-text1" class="form-control select2" name="type" disabled>
                                    <option value="error" @if($data['notification']->notification_type == 'error') selected @endif >Error</option>
                                    <option value="success" @if($data['notification']->notification_type == 'success') selected @endif>Success</option>
                                    <option value="alert"  @if($data['notification']->notification_type == 'alert') selected @endif>Alert</option>
                                    <option value="confirmation" @if($data['notification']->notification_type == 'confirmation') selected @endif>Confirmation</option>
                                </select>
                            </div>
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