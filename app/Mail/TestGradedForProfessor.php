<?php

namespace App\Mail;

use App\Models\Trial\Trial;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestGradedForProfessor extends Mailable {

    use Queueable, SerializesModels;

    /**
     * OTP constructor.
     *
     * @param string $code
     */
    public function __construct($test,$csv,$file) {
        $this->test = $test;
        $this->csv = $csv;
        $this->file = $file;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        return $this->subject($this->test['name'].' was graded')
            ->markdown('emails.test_graded_professor', [
                'test' => $this->test,
                'csv' => $this->csv
            ])->attachData(stream_get_contents($this->file), $this->csv['filename'].'.csv');
    }
}
