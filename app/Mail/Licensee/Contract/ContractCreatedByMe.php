<?php

namespace App\Mail\Licensee\Contract;

use App\Models\Contract;
use App\Models\Proposal;
use App\Models\ProposalRequest;
use App\Support\MyMailable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContractCreatedByMe extends MyMailable
{
    use Queueable, SerializesModels;

    /**
     * @var Contract
     */
    private $contract;

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
     * @param Contract $contract
     * @param Proposal $proposal
     * @param ProposalRequest $request
     */
    public function __construct(Contract $contract, Proposal $proposal, ProposalRequest $request)
    {
        $this->contract = $contract;
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
            'proposal' => $this->proposal,
            'hotel' => $this->proposal->hotel->name,
            'request'  => $this->request,
            'licensee' => $this->request->event->licensee->company_name,
            'event'    => $this->request->event->name,
            'contract' => $this->contract,
            'link'  => route(
                'licensees.contracts.show',
                [
                    'requestId' => $this->request->id,
                    'contractId'    => $this->contract->id,
                ]
            )
        ];

        return $this->view('emails.licensee.contract.created')
            ->text('emails.licensee.contract.created_plain')
            ->from(config('mail.from.address'), $data['licensee'])
            ->subject(
                sprintf(
                    'New Contract Created for %s',
                    $this->request->event->name
                )
            )
            ->with($data);
    }
}
