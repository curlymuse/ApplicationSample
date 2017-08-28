<?php

namespace App\Http\Middleware\ObjectExists;

use App\Repositories\Contracts\ClauseRepositoryInterface;
use Closure;

class ClauseExists extends ObjectExists
{
    /**
     * @var string
     */
    protected $errorMessage = 'This Clause does not exist.';

    /**
     * @var string
     */
    protected $idKey = 'clauseId';

    /**
     * Repository class
     *
     * @var string
     */
    protected $repoClass = ClauseRepositoryInterface::class;
}
