<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Analytics;
use Spatie\Analytics\Period;
use App\Http\Controllers\Backend\GoogleAnalyticsController;
use App\Models\Event;
use App\Models\Story;
use App\Models\User;
class DashboardController extends Controller
{
    public function __construct(){
        $this->google  = new GoogleAnalyticsController;   
    }

    public function index(){
    	$data['eventsCount'] = Event::where('status','<',2)->count();
        $data['storiesCount'] = Story::where('status','<',2)->count();
        $data['usersCount'] = User::where('status','<',2)->count();
       
    	$getVisitorsAndPageViews = $this->google->getVisitorsAndPageViews();
    	$fetchTopBrowsers = $this->google->fetchTopBrowsers();
    	$fetchUserLocations = $this->google->fetchUserLocations();
    	$durationPerSession = $this->google->durationPerSession();
    	$bounceRate = $this->google->bounceRate();
    	$pageViews = $this->google->pageViews();
    	
    	$visitingdates = array();
        $sitevisitors = array();
        foreach($getVisitorsAndPageViews as $visitor){
            $visitingdates[] = $visitor['date']->format('j M Y');
            $sitevisitors[] = $visitor['visitors'];
        }

        $browserused = array();
        $noOfUsers = array();
        foreach($fetchTopBrowsers as $browseruse){
            $browserused[] = $browseruse['browser'];
            $noOfUsers[] = $browseruse['sessions'];
        }

        $locationused = array();
        $noOfUsersOnLocation = array();
        foreach($fetchUserLocations as $location){
            $locationused[] = $location[0];
            $noOfUsersOnLocation[] = $location[1];
        }


    	return view('admin.dashboard')
            ->with('data',$data)
            ->with('pageViews',$pageViews)
            ->with('bounceRate',$bounceRate)
            ->with('durationPerSession',$durationPerSession)
            ->with('visitingdates',json_encode($visitingdates,JSON_NUMERIC_CHECK))
            ->with('sitevisitors',json_encode($sitevisitors,JSON_NUMERIC_CHECK))
            ->with('browserused',json_encode($browserused,JSON_NUMERIC_CHECK))      
            ->with('noOfUsers',json_encode($noOfUsers,JSON_NUMERIC_CHECK))
            ->with('locationused',json_encode($locationused,JSON_NUMERIC_CHECK))
            ->with('noOfUsersOnLocation',json_encode($noOfUsersOnLocation,JSON_NUMERIC_CHECK));
    }
}
