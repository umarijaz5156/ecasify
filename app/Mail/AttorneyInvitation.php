<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AttorneyInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $username;
    public $password;
    public $loginLink;
    public $attorneyName;

    public $companyName;
    public $companyEmail;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
       
    }
    public function invitation($data)
    {
        $this->username = $data['username'];
        $this->password = $data['password'];
        $this->loginLink = $data['loginLink'];
        $this->attorneyName = $data['attorneyName'];

        $this->companyName = env('APP_NAME');
        $this->companyEmail = env('MAIL_FROM_ADDRESS'); 

        return $this->view('email.attorney_invitation', $data)
                    ->subject('Invitation to Join '. User::getCompanyName(Auth::user()->creatorId()));
    }
    
}
