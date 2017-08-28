<?php

namespace App\Mail\Hotel\Proposal;

use App\Models\Proposal;
use App\Support\MyMailable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProposalAccepted extends MyMailable
{
    use Queueable, SerializesModels;

    /**
     * @var Proposal
     */
    private $proposal;

    /**
     * Create a new message instance.
     *
     * @param Proposal $proposal
     */
    public function __construct(Proposal $proposal)
    {
        $this->proposal = $proposal;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = [
            'licensee'  => $this->proposal->proposalRequest->event->licensee->company_name,
            'event' => $this->proposal->proposalRequest->event->name,
            'hotel' => $this->proposal->hotel->name,
        ];

        return $this->view('emails.hotel.proposal.accepted')
            ->text('emails.hotel.proposal.accepted_plain')
            ->from(config('mail.from.address'), $data['licensee'])
            ->subject(
                sprintf(
                    'Proposal for %s Selected',
                    $data['event']
                )
            )
            ->with($data);
    }
}
