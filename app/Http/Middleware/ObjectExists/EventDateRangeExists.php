<?php

namespace App\Http\Middleware\ObjectExists;

use App\Repositories\Contracts\EventDateRangeRepositoryInterface;
use Closure;

class EventDateRangeExists extends ObjectExists
{
    /**
     * @var string
     */
    protected $errorMessage = 'This Event Date Range does not exist.';

    /**
     * @var string
     */
    protected $idKey = 'eventDateRangeId';

    /**
     * Repository class
     *
     * @var string
     */
    protected $repoClass = EventDateRangeRepositoryInterface::class;
}
