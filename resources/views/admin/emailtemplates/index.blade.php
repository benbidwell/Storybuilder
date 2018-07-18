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
            <span>Email Templates</span>
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
                    <span class="caption-subject bold uppercase">Email Templates</span>
                </div>
                <div class="tools"> </div>
            </div>
            <div class="portlet-body">
                <table class="table table-striped table-bordered table-hover order-column" id="sample_1">
                    <thead>
                        <tr>
                            <th> Title </th>
                            <th> Category</th>
                            <th> Modified Date </th>
                            <th>Status</th>
                            <th> Actions </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($emailtemplates as $emailtemplate)
                            <tr class="odd gradeX">
                                <td> {{$emailtemplate->subject}} </td>
                                <td>{{@$emailtemplate->category->category_name}}</td>
                                <td class="center">
                                   <?php echo date('j M Y', strtotime($emailtemplate->updated_at));?>
                                </td>
                                <td>
                                    <span class="armdisarm emailstatus fa {{$emailtemplate->status==0 ? 'statusfield fa-lock' : 'fa-unlock'}}" name="armdisarmbutton" data-id="{{$emailtemplate->id}}" data-emailstatus="{{$emailtemplate->status}}">
                                    <span class="{{$emailtemplate->status==0 ? 'inactive' : 'active'}}">{{$emailtemplate->status==0 ? 'Inactive' : 'Active'}}</span>
                                    </span>
                                </td>

                                 <td>
                                    <div class="btn-group">
                                        <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Actions
                                            <i class="fa fa-angle-down"></i>
                                        </button>
                                        <ul class="dropdown-menu pull-left" role="menu">
                                            <li>
                                                <a href="{{url('/admin/emailtemplates')}}/<?php echo  $emailtemplate->id;?>/edit" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil"></i> Edit </a>
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
jQuery("body").on('click', ".emailstatus", function() {
    var emailid= jQuery(this).attr('data-id');
    var url = _baseUrl+'/ajax';
    var token = '{{csrf_token()}}';
    jQuery(this).toggleClass('statusfield');
    var emailstatus = jQuery(this).attr('data-emailstatus');
        if(emailstatus == 1){
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
        data: 'action=changestatusofemail&emailid='+emailid+'&_token = <?php echo csrf_token(); ?>',
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