<?php

namespace App\Mail\Client;

use App\Models\Contract;
use App\Support\MyMailable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContractSignatureRequested extends MyMailable
{
    use Queueable, SerializesModels;

    /**
     * @var Contract
     */
    private $contract;

    /**
     * Create a new message instance.
     *
     * @param Contract $contract
     */
    public function __construct(Contract $contract)
    {
        $this->contract = $contract;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = [
            'hotel'    => $this->contract->proposal->hotel->name,
            'licensee' => $this->contract->proposal->proposalRequest->event->licensee->company_name,
            'event'    => $this->contract->proposal->proposalRequest->event->name,
            'link'  => sprintf(
                '%s?h=%s&t=%s',
                route('clients.contracts.show', [$this->contract->id]),
                $this->contract->client_hash,
                $this->recipient->hash
            ),
        ];

        return $this
            ->subject(
                sprintf(
                    'Signature Requested on Contract for %s',
                    $data['event']
                )
            )
            ->from(config('mail.from.address'), $data['licensee'])
            ->view('emails.client.contract.need-signature')
            ->text('emails.client.contract.need-signature_plain')
            ->with($data);
    }
}
