<!DOCTYPE html>
<html lang="en">
 <!-- BEGIN HEAD -->
    <head>
        <meta charset="utf-8" />
        <!-- <title>{{ config('app.name', 'Laravel') }}</title> -->
        <title>Clipcrowd | Admin Panel</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="Preview page of Metronic Admin Theme #1 for statistics, charts, recent events and reports" name="description" />
        <meta content="" name="author" />
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
       	 @include('includes.head')
    <!-- END HEAD -->
    	<body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white">
    		<div class="page-wrapper">
    			<!-- BEGIN HEADER -->
            	<div class="page-header navbar navbar-fixed-top">
            		@include('includes.header')
    			</div>	
    			<!-- END HEADER -->
    			 <div class="clearfix"> </div>
    			 <!-- BEGIN CONTAINER -->
    			<div class="page-container">
    			 	<!-- BEGIN SIDEBAR -->
                	<div class="page-sidebar-wrapper">
                	@include('includes.sidebar')
                	</div>
                	<!-- END SIDEBAR -->
                	<div class="page-content-wrapper">
                		 <div class="page-content">
                		 	@yield('content')
                		 </div>
                	</div>
                	
    			</div>
    			<div class="page-footer">
    			 @include('includes.footer')
    			</div>
    		</div>
            @include('includes.foot')
             @yield('scripts')
    	</body>
        <?php // _baseUrl = ''; ?>