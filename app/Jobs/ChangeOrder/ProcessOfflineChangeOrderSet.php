<?php

namespace App\Jobs\ChangeOrder;

use App\Events\ChangeOrder\OfflineChangeOrderSetWasProcessed;
use App\Repositories\Contracts\ChangeOrderRepositoryInterface;
use App\Support\ChangeOrderParser;
use App\Support\ChangeOrderProcessor;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessOfflineChangeOrderSet implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    private $contractId;

    /**
     * @var array
     */
    private $inputData;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var string
     */
    private $userType;

    /**
     * @var array
     */
    private $labels;

    /**
     * Create a new job instance.
     *
     * @param int $contractId
     * @param array $inputData
     * @param int $userId
     * @param string $userType
     * @param array $labels
     *
     * @return void
     */
    public function __construct(
        $contractId,
        $inputData = [],
        $userId,
        $userType,
        $labels = []
    )
    {
        $this->contractId = $contractId;
        $this->inputData = $inputData;
        $this->userId = $userId;
        $this->userType = $userType;
        $this->labels = $labels;
    }

    /**
     * Execute the job.
     *
     * @param ChangeOrderParser $parser
     * @param ChangeOrderProcessor $processor
     * @param ChangeOrderRepositoryInterface $changeOrderRepo
     */
    public function handle(
        ChangeOrderParser $parser,
        ChangeOrderProcessor $processor,
        ChangeOrderRepositoryInterface $changeOrderRepo
    )
    {
        //  Parse changes
        $changes = $parser->parseChanges(
            $this->contractId,
            $this->inputData
        );

        //  Create change order set
        $changeOrderSet = $changeOrderRepo->createSet(
            $this->contractId,
            $this->userId,
            $this->userType,
            $changes,
            $this->labels
        );

        foreach ($changeOrderSet->children as $changeOrderItem) {
            $processor->process($changeOrderItem->id);
            $changeOrderRepo->accept($changeOrderItem->id, $this->userId);
        }

        event(
            new OfflineChangeOrderSetWasProcessed(
                $changeOrderSet->id
            )
        );
    }
}
