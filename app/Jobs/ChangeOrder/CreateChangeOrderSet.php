<?php

namespace App\Jobs\ChangeOrder;

use App\Events\ChangeOrder\ChangeOrderWasCreated;
use App\Jobs\Job;
use App\Repositories\Contracts\ChangeOrderRepositoryInterface;
use App\Repositories\Contracts\ContractRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Support\ChangeOrderParser;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateChangeOrderSet extends Job implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    private $contractId;

    /**
     * @var \App\Models\User
     */
    protected $user;

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
     * @var array
     */
    private $addAttachments;

    /**
     * @var array
     */
    private $removeAttachments;

    /**
     * @var null|string
     */
    private $reason;

    /**
     * Create a new job instance.
     *
     * @param int $contractId
     * @param array $inputData
     * @param array $addAttachments
     * @param array $removeAttachments
     * @param int $userId
     * @param string $userType
     * @param array $labels
     * @param string|null $reason
     */
    public function __construct(
        $contractId,
        $inputData = [],
        $addAttachments = [],
        $removeAttachments = [],
        $userId,
        $userType,
        $labels = [],
        $reason = null
    )
    {
        $this->contractId = $contractId;
        $this->inputData = $inputData;
        $this->userId = $userId;
        $this->userType = $userType;
        $this->labels = $labels;
        $this->addAttachments = $addAttachments;
        $this->removeAttachments = $removeAttachments;
        $this->reason = $reason;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        ContractRepositoryInterface $contractRepo,
        ChangeOrderRepositoryInterface $changeOrderRepo,
        UserRepositoryInterface $userRepo,
        ChangeOrderParser $parser
    )
    {
        $this->contract = $contractRepo->find($this->contractId);
        $this->user = $userRepo->find($this->userId);

        $this->verifyCompliance();

        $changes = $parser->parseChanges(
            $this->contractId,
            $this->inputData,
            $this->addAttachments,
            $this->removeAttachments
        );

        if (count($changes) == 0) {
            return null;
        }

        $changeOrder = $changeOrderRepo->createSet(
            $this->contractId,
            $this->userId,
            $this->userType,
            $changes,
            $this->labels,
            $this->reason
        );

        event(
            new ChangeOrderWasCreated(
                $changeOrder,
                $this->user,
                $this->userType
            )
        );

        return $changeOrder->id;
    }

    /**
     * @return int
     */
    public function getContractId()
    {
        return $this->contractId;
    }
}
