<?php

namespace App\Mail\Licensee;

use App\Models\Licensee;
use App\Support\MyMailable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class LicenseeUserCreated extends MyMailable
{
    use Queueable, SerializesModels;

    /**
     * @var Licensee
     */
    private $licensee;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $tempPassword;

    /**
     * Create a new message instance.
     *
     * @param Licensee $licensee
     * @param string $name
     * @param string $tempPassword
     */
    public function __construct(Licensee $licensee, $name, $tempPassword)
    {
        $this->licensee = $licensee;
        $this->name = $name;
        $this->tempPassword = $tempPassword;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = [
            'licensee'  => $this->licensee,
            'name'      => $this->name,
            'link'      => url('/login'),
            'tempPassword'  => $this->tempPassword,
        ];

        return $this->view('emails.licensee.signed-up')
            ->text('emails.licensee.signed-up_plain')
            ->from(config('mail.from.address'), $data['licensee']->company_name)
            ->subject('You now have an account. Welcome!')
            ->with($data);
    }
}
