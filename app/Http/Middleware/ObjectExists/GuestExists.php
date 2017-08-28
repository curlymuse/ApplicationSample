<?php

namespace App\Http\Middleware\ObjectExists;

use App\Repositories\Contracts\GuestRepositoryInterface;
use Closure;

class GuestExists extends ObjectExists
{
    /**
     * @var string
     */
    protected $errorMessage = 'This Guest does not exist.';

    /**
     * @var string
     */
    protected $idKey = 'guestId';

    /**
     * Repository class
     *
     * @var string
     */
    protected $repoClass = GuestRepositoryInterface::class;
}
