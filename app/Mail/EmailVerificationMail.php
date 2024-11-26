<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class EmailVerificationMail extends Mailable
{
    public $verificationUrl;

    public function __construct($verificationUrl)
    {
        $this->verificationUrl = $verificationUrl;
    }

    public function build()
    {
        return $this->subject('Verify Your Email Address')
                    ->view('emails.verify-email')
                    ->with(['verificationUrl' => $this->verificationUrl]);
    }
}