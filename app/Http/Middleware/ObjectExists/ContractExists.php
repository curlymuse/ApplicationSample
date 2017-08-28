<?php

namespace App\Http\Middleware\ObjectExists;

use App\Repositories\Contracts\ContractRepositoryInterface;
use Closure;

class ContractExists extends ObjectExists
{
    /**
     * @var string
     */
    protected $errorMessage = 'This Contract does not exist.';

    /**
     * @var string
     */
    protected $idKey = 'contractId';

    /**
     * Repository class
     *
     * @var string
     */
    protected $repoClass = ContractRepositoryInterface::class;
}
