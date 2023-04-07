<?php

namespace App\Http\Controllers;

use App\Mail\Admin\DemoCreated;
use App\Models\Demo\DemoUser;
use App\Models\Demo\TrialUser;
use App\Models\User;
use App\Util\Demo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class DemoController extends Controller {

    public function generate(Request $request) {
        $request->validate([
            'demo_email' => 'required|email',
        ]);
        $demoUserId = Artisan::call('demo:seed', ['email' => $request->get('demo_email')]);
        $this->loginUserRole($demoUserId, config('app.demo.default_role'));
        Mail::to(config('mail.from.address'))->send(new DemoCreated($request->get('demo_email')));
        return back();
    }

    public function switchRole($role, Request $request) {
        if (Demo::shouldBeAbleToSwitchRole()) {
            $demoUserId = Session::get(config('app.demo.session_field'));
            $this->loginUserRole($demoUserId, $role);
        }
        return back();
    }

    private function loginUserRole($demoUserId, $role) {
        $demoUser = DemoUser::where('id',$demoUserId)->first();
        Auth::login(User::where('email', Demo::generateEmailForRole($demoUser->email_timestamp, $role))->first());
    }
}
