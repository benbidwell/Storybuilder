<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Event;
use App\Models\StoryMedias;
class Story extends Model
{
	protected $fillable = ['story_title','story_details','story_picture','video_size','event_id','status'];
   
    public function event()
    {
        return $this->belongsTo('App\Models\Event');
    }

    public function pundit_story()
    {
        return $this->hasMany('App\Models\StoryPunditVideo');
    }

}