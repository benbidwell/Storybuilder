<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminSetting;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
  /*
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $setting=AdminSetting::first();
        return view('master')->with('setting',$setting);
    }
}
