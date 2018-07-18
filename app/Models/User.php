<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject as AuthenticatableUserContract;
use App\Models\Country;
use App\Models\Event;
class User extends Authenticatable implements AuthenticatableUserContract
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name','last_name', 'email', 'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function login(Request $request)
    {
        $input = $request->all();
        ///print_r(Auth::guard('api')->attempt(['email' => $request->input('email'), 'password' => $request->input('password')]));
        if (!$token = $this->guard()->attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
            return response()->json(['result' => 'wrong email or password.']);
        }
            return response()->json(['result' => $token]);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();  // Eloquent model method
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    //country relation with user
    public function country()
    {
        return $this->belongsTo('App\Models\Country');
    }

    // relation with events 
    public function event()
    {
        return $this->hasMany('App\Models\Event');
    }

}
