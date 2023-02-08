<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;
    public $subject;
    public $view;
    public $files;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details, $subject, $view, $files)
    {
        $this->details = $details;
        $this->subject = $subject;
        $this->view = $view;
        $this->files = $files;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->subject($this->subject)->view($this->view);

        foreach ($this->files as $key => $file) {
            $email->attach($file);
        }

        return $email;
    }
}
