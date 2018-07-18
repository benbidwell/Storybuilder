<?php
namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Analytics;
use Spatie\Analytics\Period;
class GoogleAnalyticsController extends Controller
{
    public function __construct(){
        $this->period    = Period::days(7);
    }
	//fetch total number of visitors
	public function getVisitorsAndPageViews()
    {
        $analyticsData = Analytics::fetchVisitorsAndPageViews(Period::days(7));
        return $analyticsData;
    }
    //fetch top browsers
    public function fetchTopBrowsers(){
    	$browsers = Analytics::fetchTopBrowsers(Period::days(7));
        return $browsers;
    }

    public function fetchUserLocations(){
        $matrics3 = "ga:sessions";
        $session_duration = Analytics::performQuery($this->period, $matrics3, [
                'dimensions' => 'ga:country',
                'sort' => '-ga:sessions',
            ]);
        return $session_duration['rows'];
    }

    public function durationPerSession(){
        $metrics = "ga:sessions,ga:sessionDuration";
        $duration = Analytics::performQuery($this->period, $metrics);
        $duration_per_session = gmdate('H:i:s', $duration->rows[0][1]/$duration->rows[0][0]);
       return $duration_per_session;
    }

    public function bounceRate(){
        $metrics = "ga:entrances,ga:bounces";

        $bounce = Analytics::performQuery($this->period, $metrics, [
                'dimensions' => 'ga:landingPagePath',
                'sort'       => '-ga:entrances'
            ]);
        $bounce_rate = ceil(($bounce->rows[0][2]/$bounce->rows[0][1])*100);
       
        return $bounce_rate;
    }
    public function pageViews(){
        $metrics = "ga:pageviews,ga:avgTimeOnPage,ga:pageviewsPerSession,ga:bounces,ga:entrances";

            $analytics = Analytics::performQuery($this->period, $metrics, [
                    'dimensions' => 'ga:landingPagePath',
                    'sort'       => '-ga:entrances'
                ]);
       return $analytics['rows'];
    }

    // public function totalvisitors(){
    //     $metrics = "ga:sessions,ga:avgSessionDuration,ga:hits";

    //         $analytics = Analytics::performQuery($this->period, $metrics, [
    //                 'dimensions' => 'ga:sessionDurationBucket'
    //             ]);
    //         dd($analytics);
    //    //return $analytics['rows'];
        
    // }
}