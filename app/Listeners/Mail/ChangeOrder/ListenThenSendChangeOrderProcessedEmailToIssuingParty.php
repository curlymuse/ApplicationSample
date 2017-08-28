<?php

namespace App\Listeners\Mail\ChangeOrder;

use App\Events\ChangeOrder\ChangeOrderSetWasProcessed;
use App\Mail\MultipleRecipientTypes\ChangeOrder\ChangeOrderSetProcessedByMe;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Traits\SendsMailables;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ListenThenSendChangeOrderProcessedEmailToIssuingParty
{
    use SendsMailables;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepo;

    /**
     * Create the event listener.
     *
     * @param UserRepositoryInterface $userRepo
     */
    public function __construct(UserRepositoryInterface $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * Handle the event.
     *
     * @param  ChangeOrderSetWasProcessed  $event
     * @return void
     */
    public function handle(ChangeOrderSetWasProcessed $event)
    {
        $requestId = $event->getChangeOrder()->contract->proposal->proposal_request_id;

        $repoMethod = ($event->getChangeOrder()->initiated_by_party == 'licensee')
            ? 'allManagingUsersForProposalRequest'
            : 'allRecipientsForProposalRequest';

        $users = $this->userRepo->$repoMethod($requestId);

        $this->sendMailable(
            $users,
            ChangeOrderSetProcessedByMe::class,
            [
                $event->getChangeOrder(),
                $event->getUser()
            ],
            $event->getChangeOrder()->contract
        );
    }
}
