<?php

namespace App\Mail\MultipleRecipientTypes\Contract;

use App\Models\Contract;
use App\Models\User;
use App\Support\MyMailable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContractAcceptedByOtherParty extends MyMailable
{
    use Queueable, SerializesModels;

    /**
     * @var User
     */
    private $signatory;

    /**
     * @var Contract
     */
    private $contract;

    /**
     * @var string
     */
    private $userType;

    /**
     * @var bool
     */
    private $isSignedByOtherParty;

    /**
     * Create a new message instance.
     *
     * @param User $signatory
     * @param Contract $contract
     * @param string $userType
     * @param bool $isSignedByOtherParty
     */
    public function __construct(
        User $signatory,
        Contract $contract,
        $userType,
        $isSignedByOtherParty
    )
    {
        $this->signatory = $signatory;
        $this->contract = $contract;
        $this->userType = $userType;
        $this->isSignedByOtherParty = $isSignedByOtherParty;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $hotelSignatory = $this->contract->hotelUserWhoAccepted;
        $hotelDatetime = $this->contract->accepted_by_hotel_at;
        $ownerSignatory = $this->contract->ownerUserWhoAccepted;
        $ownerDatetime = $this->contract->accepted_by_owner_at;

        $timezone = $this->contract->proposal->proposalRequest->event->licensee->timezone;

        $data = [
            'signatory' => $this->signatory->name,
            'hotel'     => $this->contract->proposal->hotel->name,
            'licensee'  => $this->contract->proposal->proposalRequest->event->licensee->company_name,
            'event'     => $this->contract->proposal->proposalRequest->event->name,
            'userTypeWhoSigned' => $this->userType,
            'hotelUserName' => ($hotelSignatory) ? $hotelSignatory->name : '',
            'hotelUserSignedAt' => ($hotelDatetime) ? timezone($hotelDatetime, $timezone, 'm/d/Y H:ia T') : '',
            'ownerUserName' => ($ownerSignatory) ? $ownerSignatory->name : '',
            'ownerUserSignedAt' => ($ownerDatetime) ? timezone($ownerDatetime, $timezone, 'm/d/Y H:ia T') : '',
            'link'  => route(
                sprintf(
                    '%ss.contracts.show',
                    ($this->userType == 'owner') ? 'hotel' : 'licensee'
                ),
                [
                    'requestId' => $this->contract->proposal->proposal_request_id,
                    'contractId'    => $this->contract->id,
                    'tab' => '?tab=agreement'
                ]
            )
        ];

        return $this->view(
            sprintf(
                'emails.licensee.contract.%s',
                ($this->isSignedByOtherParty) ? 'all-accepted' : 'accepted-need-signature'
            )
        )->subject(
            sprintf(
                'Contract Signed for %s',
                $this->contract->proposal->proposalRequest->event->name
            )
        )->from(config('mail.from.address'), $data['licensee'])
        ->with($data);
    }
}
