<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DantownNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public  $title, $body, $btn_text, $btn_url;
    public function __construct(String $title, String $body, String $btn_text, String $btn_url)
    {
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
        return $this->markdown('emails.DantownNotification');
    }
}
