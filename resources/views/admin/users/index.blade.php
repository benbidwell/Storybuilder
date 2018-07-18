 @extends('layouts.admin')
<!-- BEGIN THEME PANEL -->
@section('content')

<!-- BEGIN PAGE BAR -->
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="{{url('admin')}}">Home</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>Users</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title"> 
    <small></small>
</h1>
<!-- END PAGE TITLE-->
<div id="succ_msg"></div>
@if ($message = Session::get('success'))
<div class="alert alert-success alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button> 
        <strong>{{ $message }}</strong>
</div>
@endif
 <!-- END PAGE HEADER-->
 <div class="row">
    <div class="col-md-12">
        <!-- BEGIN EXAMPLE TABLE PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-red-sunglo">
                    <i class="icon-settings font-red-sunglo"></i>
                    <span class="caption-subject bold uppercase">Website Members</span>
                </div>
                <div class="tools"> </div>
            </div>
            <div class="portlet-body">
                <div class="table-toolbar">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="btn-group">
                                <a href="{{url('admin/users/create')}}"><button id="sample_editable_1_2_new" class="btn sbold green"> Add New
                                    <i class="fa fa-plus"></i>
                                </button></a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="btn-group pull-right">
                            </div>
                        </div>
                    </div>
                </div>
                    <table class="table table-striped table-bordered table-hover order-column" id="sample_1">
                    <thead>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Country</th>
                            <th>Joined Date</th>
                            <th>Authorized</th>
                            <th>Status</th>
                            <th>Actions</th>
                    </thead>  
                    <tbody>
                        @foreach($users as $user)
                            <tr class="odd gradeX">
                                <td> {{$user->first_name}} {{$user->last_name}} </td>
                                <td>
                                    <a href="mailto:{{$user->email}}"> {{$user->email}} </a>
                                </td>
                                <td>
                                    {{@$user->country->country_name}}
                                </td>
                                <td>
                                   <?php echo date('j M Y', strtotime($user->created_at));
                                   ?>
                                </td>
                                <td>
                                <p style="display:none">{{$user->authorized==0 ? 'Authorized' : 'Unauthorized'}}</p>
                                    <div class="switch userauthorized" data-id="{{$user->id}}">
                                      <a class=" tooltipped" id="{{$user->id}}" data-position="bottom" data-delay="50" data-tooltip="{{@$user->authorized}}">
                                      <label>
                                        <input type="checkbox" @if($user->authorized==1) checked  @endif >
                                        <span class="lever"></span>
                                        </label>
                                        </a>
                                    </div>
                                </td>
                                <td>
                                <span class="armdisarm userstatus fa {{$user->status==1 ? 'statusfield fa-lock' : 'fa-unlock'}}" name="armdisarmbutton" data-id="{{$user->id}}" data-userstatus="{{$user->status}}">
                                <span class="{{$user->status==1 ? 'inactive' : 'active'}}">{{$user->status==1 ? 'Inactive' : 'Active'}}</span>
                                </span>  
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions
                                            <i class="fa fa-angle-down"></i>
                                        </button>
                                        <ul class="dropdown-menu pull-left" role="menu">
                                            <li>
                                                 <a href="{{url('/admin/users')}}/<?php echo  $user->id;?>/edit" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil"></i> Edit </a>
                                            </li>
                                            <li>
                                            <form action="{{@url('admin/users').'/'.$user->id}}" method="post">
                                                {{ csrf_field() }}
                                                {{ method_field('DELETE') }}
                                               <a href="" data-toggle="tooltip" title="Delete"> <button type="submit"  class="deleteit"><i class="fa fa-trash-o "></i> Delete </button></a>  
                                            </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>              
               </table>
            </div>
            </div>
            </div>
        </div>
    </div>
@stop
@section('scripts')
<script type="text/javascript">
jQuery(document).ready(function () {
    jQuery(function() {
    jQuery("body").on('click', ".userstatus", function() {
        var userid= jQuery(this).attr('data-id');
        var userstatus = jQuery(this).attr('data-userstatus');
        if(userstatus == 0){
            jQuery(this).removeClass('fa-unlock');
            jQuery(this).addClass('fa-lock');
            jQuery(this).html('<span class="inactive">Inactive</span>');
        }else{
            jQuery(this).removeClass('fa-lock');
            jQuery(this).addClass('fa-unlock');
            jQuery(this).html('<span class="active">Active</span>');
        }
        var url = _baseUrl+'/admin/users/userstatuschange';
        var token = '{{csrf_token()}}';
        jQuery(this).toggleClass('statusfield');
       // jQuery(this).toggleClass("fa-lock fa-unlock");
        jQuery.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: 'POST',
        datatype: 'json',
        url: url,
        data: 'userid='+userid,
        success: function (response) 
        {
            
            jQuery('#succ_msg').html('<div class="alert alert-success alert-block"><button type="button" class="close" data-dismiss="alert">×</button>'+response+'<strong></strong></div>');
             
        },
        error: function(data, errorThrown)
          {
              alert('request failed :'+errorThrown);
          }
    });
    });
    
});

jQuery('.userauthorized').change(function(){
    var userid = jQuery(this).attr('data-id');
    var url = _baseUrl+'/admin/users/userauthorized';
    var token = '{{csrf_token()}}';
    
    jQuery.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: 'POST',
        datatype: 'json',
        url: url,
        data: 'userid='+userid,
        success: function (response) 
        {
            jQuery('#succ_msg').html('<div class="alert alert-success alert-block"><button type="button" class="close" data-dismiss="alert">×</button>'+response+'<strong></strong></div>');
        },
        error: function(data, errorThrown)
          {
              alert('request failed :'+errorThrown);
          }
    });
});
});
</script>
@stop