<?php

namespace App\Http\Middleware\ObjectExists;

use App\Repositories\Contracts\UserRepositoryInterface;
use Closure;

class UserExists extends ObjectExists
{
    /**
     * @var string
     */
    protected $errorMessage = 'This User does not exist.';

    /**
     * @var string
     */
    protected $idKey = 'userId';

    /**
     * Repository class
     *
     * @var string
     */
    protected $repoClass = UserRepositoryInterface::class;
}
