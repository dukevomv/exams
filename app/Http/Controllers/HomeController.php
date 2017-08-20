<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
Use Log;
use Session;
class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function home()
    {
        return view('home');
    }

    public function settings()
    {
        return view('settings');
    }
}
