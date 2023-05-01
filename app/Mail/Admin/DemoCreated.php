<?php

namespace App\Mail\Admin;

use App\Models\Trial\Trial;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DemoCreated extends Mailable {

    use Queueable, SerializesModels;

    /**
     * OTP constructor.
     *
     * @param string $code
     */
    public function __construct(string $demo_email) {
        $this->demo_email = $demo_email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        return $this->subject('A new Demo was created')->markdown('emails.admin.demo_created', ['demo_email' => $this->demo_email]);
    }
}
