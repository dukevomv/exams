<?php

namespace App\Traits;

use App\Models\OTP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

use Illuminate\Foundation\Auth\AuthenticatesUsers as LaravelAuthenticatesUsers;

trait AuthenticatesUsers
{
    use LaravelAuthenticatesUsers;

    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);
\Log::info($this->guard()->user()->otp_enabled);
        if($this->guard()->user()->otp_enabled == 1) {
            OTP::generateForMail($this->guard()->user()->email);
            $this->redirectTo = '/otp';
        }

        return $this->authenticated($request, $this->guard()->user())
            ?: redirect()->intended($this->redirectPath());
    }

}
