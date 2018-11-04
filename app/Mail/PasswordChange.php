<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PasswordChange extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $name;
    public $project;
    public $sent_by_name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token, $name, $project, $sent_by_name)
    {
        $this->token = $token;
        $this->name = $name;
        $this->project = $project;
        $this->sent_by_name = $sent_by_name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("You have a project login and view")
       /*  ->line("Hey, did you forget your password? Click the button below to reset it.")
        ->action('Reset Password', url('password/reset', $this->token)) */
        ->view('password_change');
    }
}
