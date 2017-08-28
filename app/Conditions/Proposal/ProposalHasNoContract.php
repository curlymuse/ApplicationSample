<?php

namespace App\Conditions\Proposal;

use App\Conditions\Condition;
use App\Jobs\Job;
use App\Models\Contract;
use App\Repositories\Contracts\ProposalRepositoryInterface;
use Exception;

class ProposalHasNoContract extends Condition
{
    /**
     * @var Proposal
     */
    private $proposal;

    /**
     * @var \App\Repositories\Contracts\ProposalRepositoryInterface
     */
    private $proposalRepo;

    /**
     * @param Job $job
     */
    public function __construct(Job $job)
    {
        $this->proposal = $job->getProposal();
        $this->proposalRepo = app(ProposalRepositoryInterface::class);
    }

    /**
     * Determine whether the job follows the condition
     *
     * @throws Exception
     * @return boolean
     */
    public function holds()
    {
        return ! $this->proposalRepo->hasContract($this->proposal->id);
    }
}
