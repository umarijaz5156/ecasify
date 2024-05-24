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


class UserNotification extends Mailable
{
    use Queueable, SerializesModels;
    
    public $username,$password,$loginLink,$name,$email,$type;
    public $companyName;
    public $companyEmail;

    //mail
    public $eventName,$calendarName,$caseName,$addedBy;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    // user added
    public function added($data)
    {
        $this->username = $data['username'];
        $this->password = $data['password'];

        $this->loginLink = $data['loginLink'];
        $this->name = $data['name'];
        $this->type = $data['type'];



        $this->companyName = User::getCompanyName(Auth::user()->creatorId());
        $this->companyEmail = User::getCompanyName(Auth::user()->creatorId(),'email');

        return $this->view('email.user_added', $data)
                    ->subject($data['subject'] ?? ' Welcome To '.$this->companyName.'');
    }
    // user updated
    public function updated($data)
    {

        $this->loginLink = $data['loginLink'];
        $this->name = $data['name'];
        $this->type = $data['type'];


        $this->companyName = User::getCompanyName(Auth::user()->creatorId());
        $this->companyEmail = User::getCompanyName(Auth::user()->creatorId(),'email');

        return $this->view('email.user_updated', $data)
                    ->subject($data['subject'] ?? 'Profile Edited Notification' );
    }

    // user deleted
    public function deleted($data)
    {
        
        $this->companyName = User::getCompanyName(Auth::user()->creatorId());
        $this->companyEmail = User::getCompanyName(Auth::user()->creatorId(),'email');
        $this->name = $data['name'] ?? 'User'; 

        return $this->view('email.user_deleted', $data)
                    ->subject($data['subject'] ?? 'Account Deleted Notification' );
    }
    //calendar added
    public function calendarAdded($data)
    {
        $this->eventName = $data['eventName'];
        $this->calendarName = $data['calendarName'];
        $this->caseName = $data['caseName'];
        $this->addedBy = $data['addedBy'];

        $this->companyName = User::getCompanyName(Auth::user()->creatorId());
        $this->companyEmail = User::getCompanyName(Auth::user()->creatorId(),'email');

        return $this->view('email.calendar_added', $data)
                    ->subject($data['subject'] ?? 'Calendar Added Notification' );
    }
    //calender updated
    public function calendarUpdated($data)
    {
        $this->eventName = $data['eventName'];
        $this->calendarName = $data['calendarName'];
        $this->caseName = $data['caseName'];
        $this->addedBy = $data['addedBy'];

        $this->companyName = User::getCompanyName(Auth::user()->creatorId());
        $this->companyEmail = User::getCompanyName(Auth::user()->creatorId(),'email');

        return $this->view('email.calendar_updated', $data)
                    ->subject($data['subject'] ?? 'Calendar Updated Notification' );
    }
    //calender deleted
    public function calendarDeleted($data)
    {
        $this->eventName = $data['eventName'];
        $this->calendarName = $data['calendarName'];
        $this->caseName = $data['caseName'];
        $this->addedBy = $data['addedBy'];

        $this->companyName = User::getCompanyName(Auth::user()->creatorId());
        $this->companyEmail = User::getCompanyName(Auth::user()->creatorId(),'email');

        return $this->view('email.calendar_deleted', $data)
                    ->subject($data['subject'] ?? 'Calendar Deleted Notification' );
    }
    
    // case added
    public function caseAdded($data)
    {
        $this->name = $data['name'];
        $this->caseName = $data['caseName'];
        $this->addedBy = $data['addedBy'];
        
        $this->companyName = User::getCompanyName(Auth::user()->creatorId());
        $this->companyEmail = User::getCompanyName(Auth::user()->creatorId(),'email');

        return $this->view('email.case_added', $data)
                    ->subject($data['subject'] ?? 'Case Added Notification' );
    }
    //case edited
    public function caseUpdated($data)
    {
        $this->eventName = $data['eventName'];
        $this->calendarName = $data['calendarName'];
        $this->caseName = $data['caseName'];
        $this->addedBy = $data['addedBy'];

        $this->companyName = User::getCompanyName(Auth::user()->creatorId());
        $this->companyEmail = User::getCompanyName(Auth::user()->creatorId(),'email'); 

        return $this->view('email.case_updated', $data)
                    ->subject($data['subject'] ?? 'Case Updated Notification' );
    }
    //case deleted
    public function caseDeleted($data)
    {
        $this->eventName = $data['eventName'];
        $this->calendarName = $data['calendarName'];
        $this->caseName = $data['caseName'];
        $this->addedBy = $data['addedBy'];

        $this->companyName = User::getCompanyName(Auth::user()->creatorId());
        $this->companyEmail = User::getCompanyName(Auth::user()->creatorId(),'email');

        return $this->view('email.case_deleted', $data)
                    ->subject($data['subject'] ?? 'Case Deleted Notification' );
    }

}
