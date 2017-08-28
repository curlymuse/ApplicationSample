<?php

namespace App\Listeners\Mail\Contract;

use App\Events\Admin\Contract\ContractWasAccepted;
use App\Mail\MultipleRecipientTypes\Contract\ContractAcceptedByMe;
use App\Repositories\Contracts\ContractRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Traits\SendsMailables;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ListenThenSendContractAcceptanceToAcceptingParty
{
    use SendsMailables;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepo;

    /**
     * @var ContractRepositoryInterface
     */
    private $contractRepo;

    /**
     * Create the event listener.
     *
     * @param UserRepositoryInterface $userRepo
     * @param ContractRepositoryInterface $contractRepo
     */
    public function __construct(UserRepositoryInterface $userRepo, ContractRepositoryInterface $contractRepo)
    {
        $this->userRepo = $userRepo;
        $this->contractRepo = $contractRepo;
    }

    /**
     * Handle the event.
     *
     * @param  ContractWasAccepted  $event
     * @return void
     */
    public function handle(ContractWasAccepted $event)
    {
        $signatory = $this->userRepo->find($event->getUserId());
        $contract = $this->contractRepo->find($event->getContractId());

        $isSignedByOtherParty = false;
        if ($event->getUserType() == 'hotel') {
            $recipients = $this->userRepo->allRecipientsForProposalRequest($contract->proposal->proposal_request_id);
            $isSignedByOtherParty = (bool)$contract->accepted_by_owner_at;
        } else {
            $recipients = $this->userRepo->allManagingUsersForProposalRequest($contract->proposal->proposal_request_id);
            $isSignedByOtherParty = (bool)$contract->accepted_by_hotel_at;
        }

        $this->sendMailable(
            $recipients,
            ContractAcceptedByMe::class,
            [
                $signatory,
                $contract,
                $event->getUserType(),
                $isSignedByOtherParty
            ],
            $contract
        );
    }
}
