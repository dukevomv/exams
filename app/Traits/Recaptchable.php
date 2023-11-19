<?php

namespace App\Traits;
use GuzzleHttp\Client;

trait Recaptchable {

    protected function requireRecaptcha($request,$callback)
    {
        if(!config('captcha.enabled')){
            return $callback();
        }
        $request->validate([
            'g-recaptcha-response' => 'required'
        ],[
            'g-recaptcha-response.required' => 'Make sure you are not a robot.'
        ]);

        $recaptchaResponse = $this->verifyRecaptcha($request->input('g-recaptcha-response'));
        
        if ($recaptchaResponse['success']) {
            return $callback();
        } else {
            return redirect()->back()->withErrors(['recaptcha' => 'ReCAPTCHA validation failed. Pintch your hand to make sure you are a human.']);
        }
    }
    
    protected function verifyRecaptcha($recaptchaResponse)
    {
        $client = new Client();
        
        $formData = [
            'secret'   => config('recaptcha.secret'),
            'response' => $recaptchaResponse,
        ];

        try {
            $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
                'form_params' => $formData,
            ]);
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Bugsnag::notifyException($e);
            \Log::error($e);
            return ['success' => false];
        }
    }
}
