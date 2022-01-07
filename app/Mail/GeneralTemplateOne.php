<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class GeneralTemplateOne extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $code, $title, $body, $btn_text, $btn_url, $name;
    public function __construct(String $title, String $body, String $btn_text, String $btn_url, String $name)
    {
        // $this->code = $code;
        $this->title = $title;
        $this->body = $body;
        $this->btn_text = $btn_text;
        $this->btn_url = $btn_url;
        $this->name = $name;
    }



    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->title)->markdown('emails.users.general_first_template');
    }
}
