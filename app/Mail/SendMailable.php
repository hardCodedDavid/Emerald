<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMailable extends Mailable
{
    use Queueable, SerializesModels;
    public $title,$name,$content,$button,$button_text,$button_link,$subject;

    /**
     * Create a new message instance.
     *
     * @param $title
     * @param $name
     * @param $content
     * @param $button
     * @param $button_text
     * @param $subject
     * @param string $button_link
     */
    public function __construct($title,$name,$content,$button,$button_text,$subject, $button_link = '#')
    {
        $this->title = $title;
        $this->name = $name;
        $this->content = $content;
        $this->button = $button;
        $this->button_text = $button_text;
        $this->subject = $subject;
        $this->button_link = $button_link;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('noreply@emeraldfarms.ng', 'Emerald Farms')
                ->subject($this->subject)
                ->view('emails.message');
    }
}
