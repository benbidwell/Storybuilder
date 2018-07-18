<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class Country extends Model
{
    // relation with user 
    public function users()
    {
        return $this->hasMany('App\Models\User')->where('status','=', 0);
    }
}
