<?php

namespace App\Mail\Licensee\Proposal;

use App\Models\Proposal;
use App\Models\ProposalRequest;
use App\Models\User;
use App\Support\MyMailable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProposalSubmittedForMyProposalRequest extends MyMailable
{
    use Queueable, SerializesModels;

    /**
     * @var User
     */
    private $userWhoSubmitted;

    /**
     * @var Proposal
     */
    private $proposal;

    /**
     * @var ProposalRequest
     */
    private $request;

    /**
     * Create a new message instance.
     *
     * @param User $userWhoSubmitted
     * @param Proposal $proposal
     * @param ProposalRequest $request
     */
    public function __construct(
        User $userWhoSubmitted,
        Proposal $proposal,
        ProposalRequest $request
    )
    {
        $this->userWhoSubmitted = $userWhoSubmitted;
        $this->proposal = $proposal;
        $this->request = $request;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = [
            'userWhoSubmitted' => $this->userWhoSubmitted,
            'hotel'            => $this->proposal->hotel->name,
            'event'            => $this->request->event->name,
            'licensee'         => $this->request->event->licensee->company_name,
            'date'             => $this->proposal->honor_bid_until->format('M d, Y'),
            'link'  => route(
                'licensees.proposals.show',
                [
                    'requestId' => $this->request->id,
                    'proposalId'    => $this->proposal->id,
                ]
            )
        ];

        return $this->view('emails.licensee.proposal.submitted')
            ->text('emails.licensee.proposal.submitted_plain')
            ->from(config('mail.from.address'), $data['licensee'])
            ->subject(
                sprintf(
                    'New Proposal Submitted for %s',
                    $data['event']
                )
            )
            ->with($data);
    }
}
