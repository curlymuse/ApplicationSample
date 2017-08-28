<?php

namespace App\Http\Middleware\ObjectExists;

use App\Repositories\Contracts\ProposalRepositoryInterface;
use Closure;

class ProposalExists extends ObjectExists
{
    /**
     * @var string
     */
    protected $errorMessage = 'This Proposal does not exist.';

    /**
     * @var string
     */
    protected $idKey = 'proposalId';

    /**
     * Repository class
     *
     * @var string
     */
    protected $repoClass = ProposalRepositoryInterface::class;
}
