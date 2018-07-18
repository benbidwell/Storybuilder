<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use Illuminate\Http\Request;
use Response,Hash;
use Validator;
use Session;

//use App\Http\Controllers\BackendController;
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
    protected $redirectTo = '/admin';
     // protected $backendController;
    //   protected $redirectTo = '/home';
    protected $redirectAfterLogout = '/admin/login';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        // $this->backendController = new BackendController(); 
    }
    public function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required|email', 
            'password' => 'required',
            // new rules here
        ]);
    }
     public function login(Request $request)
    {
        $this->validateLogin($request);

         // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $credentials = ['email' => $request->input('email'), 'password' => $request->input('password'), 'status' => 0];        

        $token = Auth::guard('members')->attempt($credentials);
        Session::put('udtoken', $token);
        
            
        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request); 

         return $this->sendFailedLoginResponse($request);
    }
  
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect('/admin');
    }

    public function credentials(Request $request)
    {
        return  array_merge($request->only($this->username(), 'password'), ['status' => 0]);
    }

    public function guard()
    {
        return Auth::guard('web');
    }
}
