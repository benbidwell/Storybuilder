<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StoryPunditVideo extends Model
{
  public function user()
  {
      return $this->hasOne('App\Models\User','id','user_id');
  }
}
