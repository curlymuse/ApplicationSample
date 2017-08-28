<?php

namespace App\Http\Middleware\ObjectExists;

use App\Repositories\Contracts\ClientRepositoryInterface;
use Closure;

class ClientExists extends ObjectExists
{
    /**
     * @var string
     */
    protected $errorMessage = 'This Client does not exist.';

    /**
     * @var string
     */
    protected $idKey = 'clientId';

    /**
     * Repository class
     *
     * @var string
     */
    protected $repoClass = ClientRepositoryInterface::class;
}
