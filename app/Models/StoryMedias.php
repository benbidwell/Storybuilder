<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Story;

class StoryMedias extends Model
{
    //
    protected $fillable = ['media_type','media_source','media_url','story_id','status'];

    
}
