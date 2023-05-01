<?php

namespace App\Mail;

use App\Models\Trial\Trial;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestGradedForStudent extends Mailable {

    use Queueable, SerializesModels;

    /**
     * OTP constructor.
     *
     * @param string $code
     */
    public function __construct($test,array $student) {
        $this->test = $test;
        $this->student = $student;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        return $this->subject($this->test['name'].' was graded')
            ->markdown('emails.test_graded_student', [
                'test' => $this->test,
                'student' => $this->student,
            ]);
    }
}
