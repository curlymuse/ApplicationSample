<?php

namespace App\Jobs\ChangeOrder;

use App\Events\ChangeOrder\ChangeOrderItemWasDeclined;
use App\Repositories\Contracts\ChangeOrderRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeclineChangeOrderItem implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    private $changeOrderId;

    /**
     * @var string
     */
    private $reason;

    /**
     * @var int
     */
    private $userId;

    /**
     * Create a new job instance.
     *
     * @param int $changeOrderId
     * @param int $userId
     * @param string $reason
     */
    public function __construct($changeOrderId, $userId, $reason)
    {
        $this->changeOrderId = $changeOrderId;
        $this->reason = $reason;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ChangeOrderRepositoryInterface $changeOrderRepo)
    {
        $changeOrderRepo->decline(
            $this->changeOrderId,
            $this->userId,
            $this->reason
        );

        event(
            new ChangeOrderItemWasDeclined(
                $this->changeOrderId,
                $this->userId,
                $this->reason
            )
        );
    }
}
