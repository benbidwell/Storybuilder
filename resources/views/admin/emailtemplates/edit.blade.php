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
            <a href="{{url('admin/emailtemplates')}}">Email Templates</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>Edit Email Template</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title">
    <small></small>
</h1>
<div id="succ_msg"></div>
@if ($message = Session::get('success'))
<div class="alert alert-success alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{ $message }}</strong>
</div>
@endif
<!-- END PAGE TITLE-->
<div class="row">
    <div class="col-md-12 ">
        <!-- BEGIN SAMPLE FORM PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-red-sunglo">
                    <i class="icon-settings font-red-sunglo"></i>
                    <span class="caption-subject bold uppercase"> Edit Email Template</span>
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
                 <form action="{{@url('admin/emailtemplates/'.$data['emailtemplate']->id)}}" method="post" enctype="multipart/form-data">
                 {{ csrf_field() }}
                 {{ method_field('PATCH') }}
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label">Purpose</label>
                            <div class="input-icon right">
                                <input type="text" value="{{$data['emailtemplate']->title}}" name="title" class="form-control"> </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label">Subject</label>
                            <div class="input-icon right">
                                <input type="text" value="{{$data['emailtemplate']->subject}}" name="subject" class="form-control"> </div>
                        </div>

                         <div class="form-group form-md-line-input">
                         <textarea class="form-control input-block-level" id="summernote_1" name="description" rows="18">{{$data['emailtemplate']->description}}</textarea>
                            <label for="form_control_1">Description</label>
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
                                    
                                    <option value="{{$category->id}}" {{ $category->id == $data['emailtemplate']->category_id ? 'selected' : '' }}
                                    >  
                                    {{$category->category_name}} </option>
                                   
                                @endforeach
                                </select>
                            </div>
                        </div> -->
                        <div class="form-group form-md-line-input">
                            <textarea class="form-control" rows="3" readonly placeholder="Enter more text" name="mergefields">{{$data['emailtemplate']->merge_fields}}</textarea>
                            <label for="form_control_1">Merge Fields</label>
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