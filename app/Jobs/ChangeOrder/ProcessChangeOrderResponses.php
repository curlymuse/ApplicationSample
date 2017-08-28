<?php

namespace App\Jobs\ChangeOrder;

use App\Events\ChangeOrder\ChangeOrderSetWasProcessed;
use App\Jobs\Job;
use App\Repositories\Contracts\ChangeOrderRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessChangeOrderResponses extends Job implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var \App\Models\ChangeOrder
     */
    protected $changeOrder;

    /**
     * @var \App\Models\User
     */
    protected $user;

    /**
     * @var int
     */
    private $changeOrderId;

    /**
     * @var string
     */
    private $userType;

    /**
     * @var int
     */
    private $contractId;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var array
     */
    private $changes;

    /**
     * Create a new job instance.
     *
     * @param int $changeOrderId
     * @param int $userId
     * @param array $changes
     */
    public function __construct($changeOrderId, $userId, $changes = [])
    {
        $this->changeOrderId = $changeOrderId;
        $this->userId = $userId;
        $this->changes = $changes;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ChangeOrderRepositoryInterface $changeOrderRepo, UserRepositoryInterface $userRepo)
    {
        $this->changeOrder = $changeOrderRepo->find($this->changeOrderId);
        $this->user = $userRepo->find($this->userId);

        $this->contractId = $this->changeOrder->contract_id;

        $this->userType = ($this->changeOrder->initiated_by_party == 'hotel') ? 'owner' : 'hotel';

        $this->verifyCompliance();

        foreach ($this->changes as $item) {

            //  Accepted items
            if ($item['accepted']) {
                dispatch(
                    new AcceptChangeOrderItem(
                        $item['id'],
                        $this->userId
                    )
                );

                continue;
            }

            //  Declined items
            dispatch(
                new DeclineChangeOrderItem(
                    $item['id'],
                    $this->userId,
                    $item['reason']
                )
            );
        }

        event(
            new ChangeOrderSetWasProcessed(
                $this->changeOrder,
                $this->user,
                $this->changes
            )
        );
    }

    /**
     * @return string
     */
    public function getUserType()
    {
        return $this->userType;
    }

    /**
     * @return int
     */
    public function getContractId()
    {
        return $this->contractId;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }
}
