<?php

namespace App\Mails;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    private $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.auth.reset')->with([
            'name' => $this->data['first_name'].' '.$this->data['last_name'],
            'email' => $this->data['email'],
            'browser' => $this->data['browser'],
            'operating_system' => $this->data['operating_system'],
            'token' => $this->data['token'],
            'referrer' => $this->data['referrer'],
        ]);
    }
}
