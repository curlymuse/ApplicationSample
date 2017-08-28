<?php

namespace App\Jobs\ChangeOrder;

use App\Events\ChangeOrder\ChangeOrderItemWasAccepted;
use App\Repositories\Contracts\ChangeOrderRepositoryInterface;
use App\Support\ChangeOrderProcessor;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AcceptChangeOrderItem implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    private $changeOrderId;

    /**
     * @var int
     */
    private $userId;

    /**
     * Create a new job instance.
     *
     * @param int $changeOrderId
     * @param int $userId
     */
    public function __construct($changeOrderId, $userId)
    {
        $this->changeOrderId = $changeOrderId;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ChangeOrderRepositoryInterface $changeOrderRepo, ChangeOrderProcessor $processor)
    {
        $processor->process($this->changeOrderId);

        $changeOrderRepo->accept(
            $this->changeOrderId,
            $this->userId
        );

        event(
            new ChangeOrderItemWasAccepted(
                $this->changeOrderId,
                $this->userId
            )
        );
    }
}
