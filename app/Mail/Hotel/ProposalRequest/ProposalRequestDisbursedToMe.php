<?php

namespace App\Mail\Hotel\ProposalRequest;

use App\Mail\Traits\NeedsProposalRequestStats;
use App\Models\Hotel;
use App\Models\ProposalRequest;
use App\Models\User;
use App\Support\MyMailable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProposalRequestDisbursedToMe extends MyMailable
{
    use Queueable, SerializesModels, NeedsProposalRequestStats;

    /**
     * @var ProposalRequest
     */
    protected $request;

    /**
     * @var Hotel
     */
    private $hotel;

    /**
     * @var array
     */
    private $otherRecipients;

    /**
     * Create a new message instance.
     *
     * @param ProposalRequest $request
     * @param Hotel $hotel
     * @param array $otherRecipients
     */
    public function __construct(
        ProposalRequest $request,
        Hotel $hotel,
        $otherRecipients = []
    )
    {
        $this->request = $request;
        $this->hotel = $hotel;
        $this->otherRecipients = $otherRecipients;
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
            'declineLink'     => $this->getDeclineLink(),
            'hotel'             => $this->hotel->name,
            'link'            => $this->getViewLink(),
            'otherRecipients'   => $this->otherRecipients,
        ];

        return $this->view('emails.hotel.proposal-request.disbursed-to-me')
            ->text('emails.hotel.proposal-request.disbursed-to-me_plain')
            ->from(config('mail.from.address'), $data['licensee'])
            ->subject(
                sprintf(
                    'New RFP for %s',
                    $data['event']
                )
            )
            ->with($data);
    }

}
