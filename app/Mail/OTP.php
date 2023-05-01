<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OTP extends Mailable {

    use Queueable, SerializesModels;

    /**
     * OTP constructor.
     *
     * @param string $code
     */
    public function __construct(string $code) {
        $this->code = $code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        return $this->subject('Your OTP for '.config('app.name'))->markdown('emails.otp', ['code' => $this->code]);
    }
}
