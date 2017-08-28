<?php

namespace App\Conditions\Contract;

use App\Conditions\Condition;
use App\Jobs\Job;
use App\Repositories\Contracts\ContractRepositoryInterface;

class ContractHasChangeOrders extends Condition
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
     * @param Job $job
     */
    public function __construct(Job $job)
    {
        $this->job = $job;
        $this->contractRepo = app(ContractRepositoryInterface::class);
    }

    /**
     * Determine whether the job follows the condition
     *
     * @return boolean
     */
    public function holds()
    {
        return $this->contractRepo->hasChangeOrders($this->job->getContractId());
    }
}