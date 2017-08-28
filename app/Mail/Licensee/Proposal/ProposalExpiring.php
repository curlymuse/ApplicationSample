<?php

namespace App\Mail\Licensee\Proposal;

use App\Models\Proposal;
use App\Support\MyMailable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProposalExpiring extends MyMailable
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
            'hotel'    => $this->proposal->hotel->name,
            'event'    => $this->proposal->proposalRequest->event->name,
            'licensee' => $this->proposal->proposalRequest->event->licensee->company_name,
            'date'     => $this->proposal->honor_bid_until->format('M d, Y'),
            'link'  => route(
                'licensees.proposals.show',
                [
                    'requestId' => $this->proposal->proposal_request_id,
                    'proposalId'    => $this->proposal->id,
                ]
            )
        ];

        return $this->view('emails.licensee.proposal.expiring')
            ->text('emails.licensee.proposal.expiring_plain')
            ->from(config('mail.from.address'), $data['licensee'])
            ->subject(
                sprintf(
                    'Proposal for %s Expiring Soon',
                    $data['event']
                )
            )
            ->with($data);
    }
}
