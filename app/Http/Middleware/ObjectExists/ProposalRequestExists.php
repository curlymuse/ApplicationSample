<?php

namespace App\Http\Middleware\ObjectExists;

use App\Repositories\Contracts\ProposalRequestRepositoryInterface;
use Closure;

class ProposalRequestExists extends ObjectExists
{
    /**
     * @var string
     */
    protected $errorMessage = 'This Proposal Request does not exist.';

    /**
     * @var string
     */
    protected $idKey = 'requestId';

    /**
     * Repository class
     *
     * @var string
     */
    protected $repoClass = ProposalRequestRepositoryInterface::class;
}
