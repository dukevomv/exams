<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Util\Demo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DemoController extends Controller {

    public function generate(Request $request) {
        $request->validate([
            'email' => 'required|email',
        ]);
        $demoUserTimestamp = Artisan::call('demo:seed', ['email' => $request->email]);
        Session::put(config('app.demo.session_field'), $demoUserTimestamp);
        $this->loginUserRole($demoUserTimestamp, config('app.demo.default_role'));
        return back();
    }

    public function switchRole($role, Request $request) {
        if (Session::has(config('app.demo.session_field'))) {
            $timestamp = Session::get(config('app.demo.session_field'));
            $this->loginUserRole($timestamp, $role);
        }
        return back();
    }

    private function loginUserRole($timestamp, $role) {
        $user = User::where('email', Demo::generateEmailForRole($timestamp, $role))->first();
        Auth::login(User::where('email', Demo::generateEmailForRole($timestamp, $role))->first());
    }
}
