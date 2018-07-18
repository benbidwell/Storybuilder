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
            <span>Notifications</span>
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
                    <span class="caption-subject bold uppercase">Notification Management</span>
                </div>
                <div class="tools"> </div>
            </div>
            <div class="portlet-body">
                <table class="table table-striped table-bordered table-hover order-column" id="sample_1">
                    <thead>
                        <tr>
                            <th> Title </th>
                            <th> Descriptions </th>
                            <!-- <th> Category</th> -->
                            <th> Type </th>
                            <th> Modified Date </th>
                            <th>Status</th>
                            <th> Actions </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($notifications as $notification)
                            <tr class="odd gradeX">
                                <td> {{$notification->main_title}}  </td>
                                <td>
                                   {{$notification->description}}
                                </td>
                                <!-- <td>
                                    {{@$notification->category->category_name}}
                                </td> -->
                                <td>
                                   {{$notification->notification_type}}
                                </td>
                                <td>
                                   <?php echo date('j M Y', strtotime($notification->updated_at));?>
                                </td>
                                <td>
                                    <span class="armdisarm notificationstatus fa {{$notification->status==0 ? 'statusfield fa-lock' : 'fa-unlock'}}" name="armdisarmbutton" data-id="{{$notification->id}}" data-notificationstatus="{{$notification->status}}">
                                    <span class="{{$notification->status==0 ? 'inactive' : 'active'}}">{{$notification->status==0 ? 'Inactive' : 'Active'}}</span>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                    <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-left" role="menu">
                                        <li>
                                            <a href="{{url('/admin/notifications')}}/<?php echo  $notification->id;?>/edit" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil"></i> Edit </a>
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
@stop
@section('scripts')
<script type="text/javascript">
jQuery(function() {
jQuery("body").on('click', ".notificationstatus", function() {

    var notificationid = jQuery(this).attr('data-id');
    var url = _baseUrl+'/ajax';
    var token = '{{csrf_token()}}';
    var notificationstatus = jQuery(this).attr('data-notificationstatus');
        if(notificationstatus == 1){
            jQuery(this).removeClass('fa-unlock');
            jQuery(this).addClass('fa-lock');
            jQuery(this).html('<span class="inactive">Inactive</span>');
        }else{
            jQuery(this).removeClass('fa-lock');
            jQuery(this).addClass('fa-unlock');
            jQuery(this).html('<span class="active">Active</span>');
        }
    jQuery.ajax({
        headers: {'X-CSRF-TOKEN': token},
        type: 'POST',
        datatype: 'json',
        url: url,
        data: 'action=changestatusofnotification&notificationid='+notificationid+'&_token = <?php echo csrf_token(); ?>',
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