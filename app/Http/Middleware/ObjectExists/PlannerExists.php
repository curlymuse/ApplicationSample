<?php

namespace App\Http\Middleware\ObjectExists;

use App\Repositories\Contracts\PlannerRepositoryInterface;
use Closure;

class PlannerExists extends ObjectExists
{
    /**
     * @var string
     */
    protected $errorMessage = 'This Planner does not exist.';

    /**
     * @var string
     */
    protected $idKey = 'plannerId';

    /**
     * Repository class
     *
     * @var string
     */
    protected $repoClass = PlannerRepositoryInterface::class;
}
