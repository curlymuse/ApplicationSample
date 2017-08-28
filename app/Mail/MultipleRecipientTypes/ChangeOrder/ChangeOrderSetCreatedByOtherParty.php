<?php

namespace App\Mail\MultipleRecipientTypes\ChangeOrder;

use App\Models\ChangeOrder;
use App\Models\User;
use App\Support\MyMailable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ChangeOrderSetCreatedByOtherParty extends MyMailable
{
    use Queueable, SerializesModels;

    /**
     * @var ChangeOrder
     */
    private $changeOrder;

    /**
     * @var User
     */
    private $user;

    /**
     * Create a new message instance.
     *
     * @param ChangeOrder $changeOrder
     * @param User $user
     * @param array $changes
     */
    public function __construct(ChangeOrder $changeOrder, User $user)
    {
        $this->changeOrder = $changeOrder;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->changeOrder->initiated_by_party == 'licensee') {
            $issuingParty = $this->changeOrder->contract->proposal->proposalRequest->event->licensee->company_name;
        } else {
            $issuingParty = $this->changeOrder->contract->proposal->hotel->name;
        }

        $changes = [];

        foreach ($this->changeOrder->children as $item) {
            $changes[] = [
                'key'      => $item->change_key,
                'label'    => $item->change_display,
                'original' => $item->original_value,
                'new'      => $item->proposed_value,
            ];
        }

        $data = [
            'stamp'        => $this->changeOrder->created_at->format('M d, Y @g:i a'),
            'author'       => $this->changeOrder->initiatedByUser->name,
            'reason'       => $this->changeOrder->reason,
            'name'         => $this->user->name,
            'issuingParty' => $issuingParty,
            'changes'      => $changes,
            'licensee'     => $this->changeOrder->contract->proposal->proposalRequest->event->licensee->company_name,
            'link'  => route(
                sprintf(
                    '%ss.contracts.show',
                    ($this->changeOrder->initiated_by_party == 'licensee') ? 'hotel' : 'licensee'
                ),
                [
                    'requestId'  => $this->changeOrder->contract->proposal->proposal_request_id,
                    'contractId' => $this->changeOrder->contract_id,
                ]
            )
        ];

        return $this->view('emails.multiple.change-order.created-by-other-party')
            ->text('emails.multiple.change-order.created-by-other-party_plain')
            ->from(config('mail.from.address'), $data['licensee'])
            ->subject(
                sprintf(
                    'Proposed Changes for %s',
                    $this->changeOrder->contract->proposal->proposalRequest->event->name
                )
            )
            ->with($data);
    }
}
