<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\EmailTemplate;
class Category extends Model
{
    //
    public function emailtemplates()
    {
        return $this->hasMany('App\Models\EmailTemplate');
    }
}
