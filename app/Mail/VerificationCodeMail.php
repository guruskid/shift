<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     *
     *
     */
    public $code, $title, $body, $btn_text, $btn_url;
    public function __construct(String $code, String $title, String $body, String $btn_text, String $btn_url)
    {
        $this->code = $code;
        $this->title = $title;
        $this->body = $body;
        $this->btn_text = $btn_text;
        $this->btn_url = $btn_url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->title)->markdown('emails.users.verification_email');
    }
}
