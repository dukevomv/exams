<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;

use Illuminate\Foundation\Auth\RegistersUsers as LaravelRegistersUsers;
use App\Traits\Recaptchable;

trait RegistersUsers
{
    use LaravelRegistersUsers;
    use Recaptchable;

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $recaptchaResponse = $this->requireRecaptcha($request,function() use ($request){
            $this->validator($request->all())->validate();
    
            event(new Registered($user = $this->create($request->all())));
    
            $this->guard()->login($user);
    
            return $this->registered($request, $user)
                            ?: redirect($this->redirectPath());
        });
        return $recaptchaResponse;
    }

}
