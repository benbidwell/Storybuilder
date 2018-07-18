<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
class Notification extends Model
{
   public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }
}
