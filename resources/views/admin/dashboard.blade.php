@extends('layouts.admin')
<!-- BEGIN THEME PANEL -->
@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.js"></script>

<script>
    var year = <?php echo $visitingdates; ?>;
    var data_click = <?php echo $visitingdates; ?>;
    var data_viewer = <?php echo $sitevisitors; ?>;
    var barChartData = {
        labels: year,
        datasets: [{
            label: 'Number of Visitors',
            backgroundColor: "rgba(151,187,205,0.5)",
            data: data_viewer
        }]
    };
//browser chart
var browserused = <?php echo $browserused; ?>;
    var data_browser = <?php echo $noOfUsers; ?>;
    var data_browser_user = <?php echo $browserused; ?>;
    var browserpieChartData = {
        labels: browserused,
        datasets: [{
            label: 'Click',
            backgroundColor: ["#0074D9", "#FF851B", "#7FDBFF", "#B10DC9","#FF4136", "#2ECC40"],
            data: data_browser
        }, {
            label: 'Browser Used',
            backgroundColor: ["#0074D9", "#FF4136", "#2ECC40"],
            data: data_browser_user
        }]
    };

//location pie chare
var locationused = <?php echo $locationused; ?>;
    var data_location = <?php echo $noOfUsersOnLocation; ?>;
    var data_location_user = <?php echo $locationused; ?>;
    var locationpieChartData = {
        labels: locationused,
        datasets: [{
            label: 'Click',
            backgroundColor: ["#0074D9", "#FF4136", "#2ECC40"],
            data: data_location
        }, {
            label: 'Browser Used',
            backgroundColor: ["#0074D9", "#FF4136", "#2ECC40"],
            data: data_location_user
        }]
    };

//initialize the graphs

    window.onload = function() {
        var ctx = document.getElementById("canvas").getContext("2d");
        window.myBar = new Chart(ctx, {
            type: 'line',
            data: barChartData,
            options: {
                elements: {
                    rectangle: {
                        borderWidth: 2,
                        borderColor: 'rgb(0, 255, 0)',
                        borderSkipped: 'bottom'
                    }
                },
                responsive: true,
                title: {
                    display: true,
                    text: 'Weekly Website Visitor'
                }
            }
        });
        var ctx1 = document.getElementById("browserdata").getContext("2d");
        window.myBar = new Chart(ctx1, {
            type: 'pie',
            data: browserpieChartData,
            options: {
                elements: {
                    rectangle: {
                        borderWidth: 2,
                        borderColor: 'rgb(0, 255, 0)',
                        borderSkipped: 'bottom'
                    }
                },
                responsive: true,
                title: {
                    display: true,
                    text: 'Browser used'
                }
            }
        });

        //for location pie chart
        var ctx2 = document.getElementById("locationdata").getContext("2d");
        window.myBar = new Chart(ctx2, {
            type: 'pie',
            data: locationpieChartData,
            options: {
                elements: {
                    rectangle: {
                        borderWidth: 2,
                        borderColor: 'rgb(0, 255, 0)',
                        borderSkipped: 'bottom'
                    }
                },
                responsive: true,
                title: {
                    display: true,
                    text: 'User Location'
                }
            }
        });
    };
</script>
<!-- BEGIN PAGE TITLE-->
<h1 class="page-title"> Admin Dashboard
    <small></small>
</h1>
<!-- END PAGE TITLE-->
<!-- BEGIN PAGE BAR -->
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="index.html">Home</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>Dashboard</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
 <!-- BEGIN DASHBOARD STATS 1-->
<div class="row">
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 blue" href="#">
            <div class="visual">
                <i class="fa fa-comments"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="{{$data['usersCount']}}">0</span>
                </div>
                <div class="desc"> Total Users </div>
            </div>
        </a>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 red" href="#">
            <div class="visual">
                <i class="fa fa-bar-chart-o"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="{{$data['eventsCount']}}">0</span></div>
                <div class="desc"> Total Events </div>
            </div>
        </a>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <a class="dashboard-stat dashboard-stat-v2 green" href="#">
            <div class="visual">
                <i class="fa fa-shopping-cart"></i>
            </div>
            <div class="details">
                <div class="number">
                    <span data-counter="counterup" data-value="{{$data['storiesCount']}}">0</span>
                </div>
                <div class="desc"> Total Stories </div>
            </div>
        </a>
    </div>
    <div id="embed-api-auth-container"></div>
<div id="chart-container"></div>
<div id="view-selector-container"></div>
</div>
<div class="row">
        <div class="col-lg- col-xs-12 col-sm-12">
            <!-- BEGIN PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-bar-chart font-dark hide"></i>
                        <span class="caption-subject font-dark bold uppercase">Site Visits</span>
                        <span class="caption-helper">weekly stats...</span>
                    </div> 
                </div>
                <div class="portlet-body">
                 <div style="margin: 20px 0 10px 30px">
                    <div class="row">
                        <div class="col-md-3 col-sm-3 col-xs-6 text-stat">
                            <span class="label label-sm label-success"> Bounce Rate : </span>
                            <h3>{{$bounceRate}}%</h3>
                        </div>
                        <div class="col-md-3 col-sm-3 col-xs-6 text-stat">
                            <span class="label label-sm label-info"> Per Session Duration: </span>
                            <h3>{{$durationPerSession}}</h3>
                        </div>
                        <!-- <div class="col-md-3 col-sm-3 col-xs-6 text-stat">
                            <span class="label label-sm label-danger"> Shipment: </span>
                            <h3>$1,134</h3>
                        </div>
                        <div class="col-md-3 col-sm-3 col-xs-6 text-stat">
                            <span class="label label-sm label-warning"> Orders: </span>
                            <h3>235090</h3>
                        </div> -->
                    </div>
                </div>
                <canvas id="canvas" height="280" width="600"></canvas>
                </div>
            </div>
            <!-- END PORTLET-->
        </div>  
    </div>
    
<div class="clearfix"></div>

<!-- END DASHBOARD STATS 1-->

<div class="row">
    <div class="col-lg-6 col-xs-12 col-sm-12">
        <!-- BEGIN PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-bar-chart font-dark hide"></i>
                    <span class="caption-subject font-dark bold uppercase">Browser Used</span>
                    <span class="caption-helper">weekly stats...</span>
                </div>
               
            </div>
            <div class="portlet-body">
            <canvas id="browserdata" height="280" width="600"></canvas>
            </div>
        </div>
        <!-- END PORTLET-->
    </div>
    <div class="col-lg-6 col-xs-12 col-sm-12">
        <!-- BEGIN PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-bar-chart font-dark hide"></i>
                    <span class="caption-subject font-dark bold uppercase">User Location</span>
                    <span class="caption-helper">weekly stats...</span>
                </div>
               
            </div>
            <div class="portlet-body">
            <canvas id="locationdata" height="280" width="600"></canvas>
            </div>
        </div>
        <!-- END PORTLET-->
    </div>
    </div>

<div class="clearfix"></div>
<div class="row">
    <div class="col-lg-12 col-xs-12 col-sm-12">
        <!-- BEGIN PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-bar-chart font-dark hide"></i>
                    <span class="caption-subject font-dark bold uppercase">Page Views</span>
                    <span class="caption-helper">weekly stats...</span>
                </div>
               
            </div>
            <div class="portlet-body">
<table class="table table-striped table-bordered table-hover order-column" id="sample_1">
    <thead>
        <tr>
            <th>Path </th>
            <th>Page Views</th>
            <th>Page Views Per Session</th>
            <th>Average Time on Page</th>
            <th>Bounce Rate</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($pageViews as $pageview)
            <tr class="odd gradeX">
                <td> <a href="http:/clipcrowd.cdemo.in/{{$pageview[0]}}">{{$pageview[0]}} </a></td>
                <td>
                    {{$pageview[1]}} 
                </td>
                <td>
                   {{$pageview[2]}} 
                </td>
                 <td> 
                 {{$pageview[3]}} 
                </td>
                 <td> 
                 {{$pageview[4]}} 
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
    jQuery(document).ready(function () {
        var token = '<?php echo Session::get('udtoken'); ?>';
            window.localStorage.setItem('ud', token);
            var accessed  = window.localStorage.getItem('ud');
});
</script>

@stop