<?php

namespace App\Listeners\Mail\ChangeOrder;

use App\Events\ChangeOrder\ChangeOrderWasCreated;
use App\Mail\MultipleRecipientTypes\ChangeOrder\ChangeOrderSetCreatedByOtherParty;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Traits\SendsMailables;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ListenThenSendChangeOrderCreatedEmailToReceivingParty
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
     * @param  ChangeOrderWasCreated  $event
     * @return void
     */
    public function handle(ChangeOrderWasCreated $event)
    {
        $requestId = $event->getChangeOrder()->contract->proposal->proposal_request_id;

        $repoMethod = ($event->getChangeOrder()->initiated_by_party == 'licensee')
            ? 'allRecipientsForProposalRequest'
            : 'allManagingUsersForProposalRequest';

        $users = $this->userRepo->$repoMethod($requestId);

        $this->sendMailable(
            $users,
            ChangeOrderSetCreatedByOtherParty::class,
            [
                $event->getChangeOrder(),
                $event->getUser()
            ],
            $event->getChangeOrder()->contract
        );
    }
}
