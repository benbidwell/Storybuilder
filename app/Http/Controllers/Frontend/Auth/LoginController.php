<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use Illuminate\Http\Request;
use Response,Hash;
use Validator;
use App\Http\Controllers\FrontendController;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';


    protected $frontendController;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->frontendController = new FrontendController(); 
    }


    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    
    public function login(Request $request)
    {  

        

        $rules = [
            'email'         => 'required|email',
            'password'      => 'required'
        ];

        $messages = [       
            'email.required'        => $this->frontendController->notificationMessage('login_email_required','error'),
            'email.email'           => $this->frontendController->notificationMessage('login_email_email','error'),
            'password.required'     => $this->frontendController->notificationMessage('login_password_required','error'),
        ]; 

        $validator = Validator::make($request->all(), $rules,$messages);

        if(!$validator->fails()){        
        
            $credentials = ['email' => $request->input('email'), 'password' => $request->input('password'), 'status' => 0];        

            if ($token = $this->guard()->attempt($credentials))
            {
                return $this->sendLoginResponse($request, $token);
            }        

            return $this->sendFailedLoginResponse($request);
        }else{
            // on error return error messages array
            return $this->frontendController->setResponseFormat(400, 'Validation Errors',$validator->messages());
        }
    }

    

    protected function sendLoginResponse(Request $request, $token)
    {        

        $this->clearLoginAttempts($request);

        return $this->authenticated($request, $this->guard()->user(), $token);

        // return $this->authenticated($request, $this->guard()->user())
        //         ?: redirect()->intended($this->redirectPath());
    }

    protected function authenticated(Request $request, $user, $token)
    {         
        if($user->authorized == 0){
            $data = array('is_authorized'=>'false');
        }else{
            $data = array('is_authorized'=>'true');
        }
       
        return $this->frontendController->setResponseFormat(200, $this->frontendController->notificationMessage('user_logged_in','success'),$data,$token);
    }

    protected function guard()
    {
        return Auth::guard('members');
    }

    protected function sendFailedLoginResponse(Request $request){ 
        $frontendController = new FrontendController();       
        return $this->frontendController->setResponseFormat(400, $this->frontendController->notificationMessage('invalid_login_credentials','error'));
    }

}
