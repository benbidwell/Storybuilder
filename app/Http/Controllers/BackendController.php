<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response, Route;
use App\Models\Notification;
use App\Models\EmailTemplate;
use App\Http\Controllers\AjaxController;
class BackendController extends Controller
{
    public function __construct(){

    }

    protected $resultFormat = array(
	        'message'      => ':message',
	        'data' 	       => ':data',
            'token'        => ':token',
            'code'         => ':code',
    	);

    public function setResponseFormat($statusCode = 200, $message, $data = NULL, $token = null, $code = 1){
    	$this->resultFormat = [
    		'message'  => $message,
    		'data'	   => $data,
            'token'    => $token,
            'code'     => $code,
    	];

    	return Response::json($this->resultFormat, $statusCode)
            ->header('Access-Control-Allow-Headers', '*')
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', '*')
            ->header('Content-Type', 'application/json')
            ->header('Access-Control-Allow-Credentials', true)
            ->header('HTTP_Authorization', 'Bearer ' . $token);
    }

    public function notificationMessage($title = NULL,$notification_type = NULL,$status = 1){
        $notification = Notification::where('title', $title)->where('notification_type',$notification_type)->where('status',$status)->first();
        return $notification->description;
    }

    public function emailTemplate($purpose = NULL,$status = 1){
        $emailbody = EmailTemplate::where('purpose', $purpose)->where('status',$status)->select('subject', 'description')->first();
        return $emailbody;
    }

}
