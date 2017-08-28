<?php

namespace App\Mail\Hotel;

use App\Models\User;
use App\Models\Licensee;
use App\Support\MyMailable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserAssignedFirstPassword extends MyMailable
{
    use Queueable, SerializesModels;

    /**
     * @var
     */
    private $password;

    /**
     * @var
     */
    private $licensee;

    /**
     * @var
     */
    private $user;


    /**
     * Create a new message instance.
     *
     * @param $password
     */
    public function __construct($password, User $user, Licensee $licensee)
    {
        $this->password = $password;
        $this->user = $user;
        $this->licensee = $licensee;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = [
            'name'     => $this->user->name,
            'licensee' => $this->licensee->company_name,
            'password' => $this->password,
            'email'    => $this->user->email,
            'link'     => url('/login'),
        ];

        return $this
            ->view('emails.hotel.user.first_password')
            ->text('emails.hotel.user.first_password_plain')
            ->from(config('mail.from.address'), $data['licensee'])
            ->with($data);
    }
}
