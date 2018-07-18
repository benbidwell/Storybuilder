<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Story;
use App\Models\User;
class Event extends Model
{
   protected $fillable = ['event_title','event_details','event_picture','user_id','status'];

    public function stories()
    {
        return $this->hasMany('App\Models\Story');
    }

    public function publishedStories()
    {
        return $this->hasMany('App\Models\Story')->where('publish_status', '=', 1);
    }

    //event relation with user
   	public function user()
    {
        return $this->belongsTo('App\Models\User');
    }


}