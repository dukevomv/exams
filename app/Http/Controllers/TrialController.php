<?php

namespace App\Http\Controllers;

use App\Mail\Admin\TrialCreated;
use App\Models\Demo\TrialUser;
use App\Models\Trial\Trial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use App\Traits\Recaptchable;

class TrialController extends Controller {

    use Recaptchable;
    
    public function index(){
        return view('trial');
    }

    public function generate(Request $request) {
        $recaptchaResponse = $this->requireRecaptcha($request,function() use ($request){
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
            $this->loginUser($trial);
            Mail::to(config('mail.from.address'))->send(new TrialCreated($trial));
            return true;
        });
        
        return redirect('otp')->with(['success' => 'A one time password has been sent to your email.']);
    }

    public function sendLoginCode(Request $request) {
        $recaptchaResponse = $this->requireRecaptcha($request,function() use ($request){
            $request->validate(['trial_email' => 'required|email']);
            $trial = Trial::where('email',$request->get('trial_email'))->first();
            if(is_null($trial)){
                return back()->with(['error' => 'Trial with this email was not found. Fill the form below and Start your Trial.']);
            }
            $this->loginUser($trial);
            return true;
        });
        return redirect('otp')->with(['success' => 'A one time password has been sent to your email.']);
    }

    private function loginUser($trial) {
        $user = $trial->sendPendingOTPAndGetUser();
        Session::put(config('app.trial.session_field'), $trial->id);
        Auth::login($user);
    }
}
