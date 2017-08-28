<?php

namespace App\Conditions\Contract;

use App\Conditions\Condition;
use App\Jobs\Job;
use App\Models\Client;
use App\Repositories\Contracts\ContractRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;

class ContractOwnerActionMatchesUser extends Condition
{
    /**
     * @var Job
     */
    protected $job;

    /**
     * @var \App\Repositories\Contracts\ContractRepositoryInterface
     */
    private $contractRepo;

    /**
     * @var \App\Repositories\Contracts\UserRepositoryInterface
     */
    private $userRepo;

    /**
     * @param Job $job
     */
    public function __construct(Job $job)
    {
        $this->job = $job;
        $this->contractRepo = app(ContractRepositoryInterface::class);
        $this->userRepo = app(UserRepositoryInterface::class);
    }

    /**
     * Determine whether the job follows the condition
     *
     * @return boolean
     */
    public function holds()
    {
        if (! $this->contractRepo->isClientOwned($this->job->getContractId())) {
            return true;
        }

        if ($this->job->getUserType() != 'owner') {
            return true;
        }

        $contract = $this->contractRepo->find($this->job->getContractId());

        return $this->userRepo->hasRole(
            $this->job->getUserId(),
            'client',
            Client::class,
            $contract->proposal->proposalRequest->client_id
        );
    }
}