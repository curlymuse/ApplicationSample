<?php

namespace App\Mail\Hotel;

use App\Models\Licensee;
use App\Models\User;
use App\Support\MyMailable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class HotelUserWelcomed extends MyMailable
{
    use Queueable, SerializesModels;

    /**
     * @var User
     */
    private $user;

    /**
     * @var Licensee
     */
    private $licensee;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param Licensee $licensee
     */
    public function __construct(User $user, Licensee $licensee)
    {
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
            'name'  => $this->user->name,
            'licensee'  => $this->licensee->company_name,
        ];

        return $this->view('emails.hotel.user.welcome')
            ->text('emails.hotel.user.welcome_plain')
            ->from(config('mail.from.address'), $data['licensee'])
            ->subject('Welcome!')
            ->with($data);
    }
}
