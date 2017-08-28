<?php

namespace App\Http\Middleware\ObjectExists;

use App\Repositories\Contracts\EventRepositoryInterface;
use Closure;

class EventExists extends ObjectExists
{
    /**
     * @var string
     */
    protected $errorMessage = 'This Event does not exist.';

    /**
     * @var string
     */
    protected $idKey = 'eventId';

    /**
     * Repository class
     *
     * @var string
     */
    protected $repoClass = EventRepositoryInterface::class;
}
