<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
class EmailTemplate extends Model
{
    //category relation with user
    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }
}
