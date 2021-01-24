<?php

namespace App\Traits;

use App\Models\OTP;
use Illuminate\Foundation\Auth\AuthenticatesUsers as LaravelAuthenticatesUsers;
use Illuminate\Http\Request;

trait AuthenticatesUsers {

    use LaravelAuthenticatesUsers;

    protected function sendLoginResponse(Request $request) {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);
        if ($this->guard()->user()->otp_enabled == 1) {
            OTP::generateForMail($this->guard()->user()->email);
            $this->redirectTo = '/otp';
        }

        return $this->authenticated($request, $this->guard()->user())
            ?: redirect()->intended($this->redirectPath());
    }

}
