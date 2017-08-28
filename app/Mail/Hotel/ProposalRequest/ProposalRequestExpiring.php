<?php

namespace App\Mail\Hotel\ProposalRequest;

use App\Mail\Mail\Hotel\Proposal\ProposalDeclined;
use App\Mail\Traits\NeedsProposalRequestStats;
use App\Models\Hotel;
use App\Models\ProposalRequest;
use App\Support\MyMailable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProposalRequestExpiring extends MyMailable
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
     * Create a new message instance.
     *
     * @param ProposalRequest $request
     * @param Hotel $hotel
     */
    public function __construct(ProposalRequest $request, Hotel $hotel)
    {
        $this->request = $request;
        $this->hotel = $hotel;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = [
            'hotel' => $this->hotel,
            'request'   => $this->request,
            'licensee'          => $this->request->event->licensee->company_name,
            'event'             => $this->request->event->name,
            'date'          => $this->request->cutoff_date->format('M d, Y'),
            'link'      => $this->getViewLink(),
            'declineLink'   => $this->getDeclineLink(),
        ];

        return $this->view('emails.hotel.proposal-request.expiring')
            ->text('emails.hotel.proposal-request.expiring_plain')
            ->from(config('mail.from.address'), $data['licensee'])
            ->subject(
                sprintf(
                    'RFP for %s Expiring Soon',
                    $data['event']
                )
            )
            ->with($data);
    }
}
