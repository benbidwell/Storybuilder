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
            <span>Events</span>
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
                    <span class="caption-subject bold uppercase">Events Management</span>
                </div>
                <div class="tools"> </div>
            </div>
            <div class="portlet-body">
                <table class="table table-striped table-bordered table-hover order-column" id="sample_1">
                    <thead>
                        <tr>
                            <th> Event Title  </th>
                            <th class="numeric"> Name of the User </th>
                            <th class="numeric"> Modified Date</th>
                            <th class="numeric"> Number of Story </th>
                            <th class="numeric">Status </th>
                            <th class="numeric">Actions </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($events as $event)
                            <tr class="odd gradeX">
                                <td> {{$event->event_title}}  </td>
                                <td>
                                    {{@$event->user->first_name}} {{@$event->user->last_name}}
                                </td>
                                <td>
                                   <?php echo date('j M Y', strtotime($event->updated_at));?>
                                </td>
                                <td>
                                   {{$event->storiesCount}}
                                </td>
                                <td>
                                <span class="armdisarm eventstatus fa {{$event->status==1 ? 'statusfield fa-lock' : 'fa-unlock'}}" name="armdisarmbutton" data-id="{{$event->id}}" data-eventstatus="{{$event->status}}">
                                    <span class="{{$event->status==1 ? 'inactive' : 'active'}}">{{$event->status==1 ? 'Inactive' : 'Active'}}</span>
                                </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                    <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-left" role="menu">
                                        <li>
                                            <a href="{{url('/admin/events')}}/<?php echo  $event->id;?>/edit" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil"></i> Edit </a>
                                        </li>
                                        <li>
                                        <form action="{{@url('admin/events').'/'.$event->id}}" method="post">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                           <a href="" data-toggle="tooltip" title="Delete" > <button type="submit" class="deleteit"><i class="fa fa-trash-o "></i> Delete </button></a> 
                                        
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
@stop
@section('scripts')
<script type="text/javascript">
jQuery(function() {
jQuery("body").on('click', ".eventstatus", function() {
    var eventid= jQuery(this).attr('data-id');
    var url = _baseUrl+'/ajax';
    var token = '{{csrf_token()}}';
    jQuery(this).toggleClass('statusfield');
    var eventstatus = jQuery(this).attr('data-eventstatus');
        if(eventstatus == 0){
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
        data: 'action=changestatusofevent&eventid='+eventid+'&_token = <?php echo csrf_token(); ?>',
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