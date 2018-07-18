<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class AdminSetting extends Model
{
    protected $table = 'admin_settings';
    protected $fillable = ['smtp_host','smtp_host','smtp_username','smtp_password','google_analytics_code','pundit_title_text','facebook_pixel_code','trasition_time'];

}