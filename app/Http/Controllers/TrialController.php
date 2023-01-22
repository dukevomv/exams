<?php

namespace App\Http\Controllers;

use App\Models\Demo\TrialUser;
use App\Models\OTP;
use App\Models\Trial\Trial;
use App\Models\User;
use App\Util\Demo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class TrialController extends Controller {

    public function generate(Request $request) {
        $request->validate([
            'trial_email' => 'required|email|unique:trials,email',
            'course_name' => 'required|string',
            'scheduled_at' => 'required|date_format:Y-m-d\TH:i|after:today',
            'duration_in_minutes' => 'required|integer|max:300',
            'reason' => 'required|string',
        ]);
        $trial = Trial::create(array_merge(['email' => $request->get('trial_email')],[
                'details' => $request->only(['scheduled_at','course_name','duration_in_minutes','reason']),
                'status' => 'started'
            ]));
        Session::put(config('app.trial.session_field'), $trial->id);
        Artisan::call('trial:seed', ['trial_id' => $trial->id]);
        $this->loginUserRole($trial, config('app.trial.default_role'));
        return back();
    }

    public function switchRole($role, Request $request) {
        if (Session::has(config('app.trial.session_field'))) {
            $trialId = Session::get(config('app.trial.session_field'));
            $trial = Trial::findOrFail($trialId);
            $this->loginUserRole($trial, $role);
        }
        return back();
    }

    public function sendLoginCode(Request $request) {
        //todo - make otp flow to be the first thing for user
        //todo - do the same for students
        //todo - if otp in profile is used, disable editing of profile
        $trial = Trial::where('email',$request->get('trial_email'))->firstOrFail();
        Auth::login($trial->getProfessorUser());
        return back()->with(['success' => 'A one time password has been send to your email.']);
    }

    private function loginUserRole($trial, $role) {
        Auth::login(User::where('email', Trial::generateEmailForRole($trial->uuid, $role))->first());
    }
    //todo - add human since in test scheduled at and list of tests
    //todo - add  invite of users on lesson or test directly
    //todo - hide all other tests and courses from students invited for specific trial
    //todo - add student limit
    //todo - filter professor available courses
    //todo - hide from default installation the trial and demo courses
    //todo - maybe move all data into one table?
    //todo - login email and errors and validation should fill correct forms
    //todo - create codes for each invite and professor to be able to rejoin (generate OTPs on login)
}
