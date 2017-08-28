<?php

namespace App\Mail\Licensee\ProposalRequest;

use App\Mail\Traits\NeedsProposalRequestStats;
use App\Models\ProposalRequest;
use App\Support\MyMailable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProposalRequestDisbursedFromMyStaff extends MyMailable
{
    use Queueable, SerializesModels, NeedsProposalRequestStats;

    /**
     * @var ProposalRequest
     */
    protected $request;

    /**
     * Create a new message instance.
     *
     * @param ProposalRequest $request
     */
    public function __construct(ProposalRequest $request)
    {
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
            'event'           => $this->request->event->name,
            'description'     => $this->request->event->description,
            'dates'           => $this->getDateString(),
            'location'        => $this->getLocationString(),
            'eventType'       => ($this->request->event->event_sub_type_id) ? $this->request->event->subType->name : null,
            'eventTypeIcon'   => ($this->request->event->event_sub_type_id) ? $this->request->event->subType->icon : null,
            'roomNights'      => $this->getRoomNights(),
            'meetingSpace'    => $this->request->is_meeting_space_required,
            'foodAndBeverage' => $this->request->is_food_and_beverage_required,
            'emailBanner'     => $this->request->event->licensee->email_banner,
            'licensee'        => $this->request->event->licensee->company_name,
            'licenseeUserName'=> $this->request->author->name,
            'licenseeUserEmail'=> $this->request->author->email,
            'licenseePhone'   => $this->request->event->licensee->phone,
            'link'  => route(
                'licensees.events.show',
                [
                    'requestId' => $this->request->id,
                ]
            )
        ];

        return $this->view('emails.licensee.proposal-request.disbursed-from-my-staff')
            ->text('emails.licensee.proposal-request.disbursed-from-my-staff_plain')
            ->from(config('mail.from.address'), $data['licensee'])
            ->subject(
                sprintf(
                    'New %s RFP Sent Out',
                    $data['event']
                )
            )
            ->with($data);
    }
}
