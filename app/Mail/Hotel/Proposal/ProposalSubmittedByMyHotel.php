<?php

namespace App\Mail\Hotel\Proposal;

use Carbon\Carbon;
use App\Models\Proposal;
use App\Models\ProposalRequest;
use App\Models\User;
use App\Support\MyMailable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProposalSubmittedByMyHotel extends MyMailable
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
     * @var int
     */
    private $eventDateRangeId;

    /**
     * Create a new message instance.
     *
     * @param User $userWhoSubmitted
     * @param Proposal $proposal
     * @param ProposalRequest $request
     * @param int $eventDateRangeId
     */
    public function __construct(
        User $userWhoSubmitted,
        Proposal $proposal,
        ProposalRequest $request,
        $eventDateRangeId
    )
    {
        $this->userWhoSubmitted = $userWhoSubmitted;
        $this->proposal = $proposal;
        $this->request = $request;
        $this->eventDateRangeId = $eventDateRangeId;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $timezone = $this->request->event->licensee->timezone;

        $submittedAtRaw = $this->proposal
            ->dateRanges()
            ->whereEventDateRangeId($this->eventDateRangeId)
            ->first()
            ->submitted_at;

        $data = [
            'userWhoSubmitted'  => $this->userWhoSubmitted->name,
            'proposal'          => $this->proposal,
            'request'           => $this->request,
            'licensee'          => $this->request->event->licensee->company_name,
            'event'             => $this->request->event->name,
            'submittedDateTime'     => Carbon::parse($submittedAtRaw)->timezone($timezone)->format('m/d/Y H:ia T'),
            'licenseeUserName'  => $this->request->author->name,
            'licenseeUserEmail' => $this->request->author->email,
            'licenseePhone'     => $this->request->event->licensee->phone,
        ];

        return $this->view('emails.hotel.proposal.submitted')
            ->from(config('mail.from.address'), $data['licensee'])
            ->subject(
                sprintf(
                    'Proposal for %s Submitted',
                    $data['event']
                )
            )
            ->text('emails.hotel.proposal.submitted_plain')
            ->with($data);
    }
}
