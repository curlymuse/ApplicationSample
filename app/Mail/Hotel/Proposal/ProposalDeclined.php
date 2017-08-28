<?php

namespace App\Mail\Hotel\Proposal;

use App\Models\Proposal;
use App\Models\User;
use App\Support\MyMailable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProposalDeclined extends MyMailable
{
    use Queueable, SerializesModels;

    /**
     * @var Proposal
     */
    private $proposal;

    /**
     * @var User
     */
    private $userWhoDeclined;

    /**
     * @var null|string
     */
    private $declinedBecause;

    /**
     * Create a new message instance.
     *
     * @param Proposal $proposal
     * @param User $userWhoDeclined
     * @param string|null $declinedBecause
     *
     * @internal param User $user
     */
    public function __construct(Proposal $proposal, User $userWhoDeclined, $declinedBecause = null)
    {
        $this->proposal = $proposal;
        $this->userWhoDeclined = $userWhoDeclined;
        $this->declinedBecause = $declinedBecause;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = [
            'licensee'          => $this->proposal->proposalRequest->event->licensee->company_name,
            'event'             => $this->proposal->proposalRequest->event->name,
            'reason'            => $this->declinedBecause,
            'userWhoDeclined'   => $this->userWhoDeclined->name,
            'licenseeUserName'  => $this->proposal->proposalRequest->author->name,
            'licenseeUserEmail' => $this->proposal->proposalRequest->author->email,
            'licenseePhone'     => $this->proposal->proposalRequest->event->licensee->phone,
        ];

        return $this->view('emails.hotel.proposal.declined')
            ->text('emails.hotel.proposal.declined_plain')
            ->from(config('mail.from.address'), $data['licensee'])
            ->subject(
                sprintf(
                    'Proposal for %s Declined',
                    $data['event']
                )
            )
            ->with($data);
    }
}
