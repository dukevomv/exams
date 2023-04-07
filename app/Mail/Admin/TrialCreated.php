<?php

namespace App\Mail\Admin;

use App\Models\Trial\Trial;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TrialCreated extends Mailable {

    use Queueable, SerializesModels;

    /**
     * OTP constructor.
     *
     * @param string $code
     */
    public function __construct(Trial $trial) {
        $this->trial = $trial;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        return $this->subject('A new Trial was created')->markdown('emails.admin.trial_created', ['trial' => $this->trial]);
    }
}
