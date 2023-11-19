<?php

namespace App\Traits;

use App\Models\OTP;
use Illuminate\Foundation\Auth\AuthenticatesUsers as LaravelAuthenticatesUsers;
use Illuminate\Http\Request;

use App\Traits\Recaptchable;

trait AuthenticatesUsers {

    use LaravelAuthenticatesUsers;
    use Recaptchable;
    
    protected function sendLoginResponse(Request $request) {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);
        if ($this->guard()->user()->otp_enabled == 1) {
            OTP::generateForMail($this->guard()->user()->email,$this->guard()->user());
            $this->redirectTo = '/otp';
        }

        return $this->authenticated($request, $this->guard()->user())
            ?: redirect()->intended($this->redirectPath());
    }
    
    public function login(Request $request)
    {
        $recaptchaResponse = $this->requireRecaptcha($request,function() use ($request){
            $this->validateLogin($request);

            // If the class is using the ThrottlesLogins trait, we can automatically throttle
            // the login attempts for this application. We'll key this by the username and
            // the IP address of the client making these requests into this application.
            if ($this->hasTooManyLoginAttempts($request)) {
                $this->fireLockoutEvent($request);
    
                return $this->sendLockoutResponse($request);
            }
    
            if ($this->attemptLogin($request)) {
                return $this->sendLoginResponse($request);
            }
    
            // If the login attempt was unsuccessful we will increment the number of attempts
            // to login and redirect the user back to the login form. Of course, when this
            // user surpasses their maximum number of attempts they will get locked out.
            $this->incrementLoginAttempts($request);

            return $this->sendFailedLoginResponse($request);
        });
        return $recaptchaResponse;
    }

}
