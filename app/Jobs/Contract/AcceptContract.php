<?php

namespace App\Jobs\Contract;

use App\Events\Admin\Contract\ContractWasAccepted;
use App\Jobs\Job;
use App\Repositories\Contracts\ContractRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AcceptContract extends Job implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    private $contractId;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var string
     */
    private $userType;

    /**
     * @var string
     */
    private $signature;

    /**
     * Create a new job instance.
     *
     * @param int $contractId
     * @param int $userId
     * @param string $userType
     * @param string $signature
     */
    public function __construct($contractId, $userId, $userType, $signature)
    {
        $this->contractId = $contractId;
        $this->userId = $userId;
        $this->userType = $userType;
        $this->signature = $signature;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ContractRepositoryInterface $contractRepo)
    {
        $this->verifyCompliance();

        switch ($this->userType) {
            case 'hotel':
                $method = 'acceptForHotel';
                break;
            case 'owner':
                $method = 'acceptForOwner';
                break;
            default:
                throw new MalformedJobInputException('Invalid user type.');
                break;
        }

        $contractRepo->$method($this->contractId, $this->userId, $this->signature);

        $contract = $contractRepo->find($this->contractId);

        event(
            new ContractWasAccepted(
                $contract,
                $this->userId,
                $this->userType,
                $this->signature
            )
        );
    }

    /**
     * @return int
     */
    public function getContractId()
    {
        return $this->contractId;
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
    public function getUserId()
    {
        return $this->userId;
    }
}
