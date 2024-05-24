<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserLoginNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $username;
    public $ipAddress;
    public $device;
    public $browser;
    public $location;
    public $loginTime;
    public $companyName;
    public $companyEmail;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->username = $data['username'];
        $this->ipAddress = $data['ipAddress'];
        $this->device = $data['device'];
        $this->browser = $data['browser'];
        $this->location = $data['location'];
        $this->loginTime = $data['loginTime'];
        $this->companyName = $data['companyName'];
        $this->companyEmail = $data['companyEmail'];
    }
/**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->companyName .' Login Notification')
                    ->view('email.login_notification');
    }
}
