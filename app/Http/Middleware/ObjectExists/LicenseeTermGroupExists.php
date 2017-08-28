<?php

namespace App\Http\Middleware\ObjectExists;

use App\Repositories\Contracts\LicenseeTermGroupRepositoryInterface;
use Closure;

class LicenseeTermGroupExists extends ObjectExists
{
    /**
     * @var string
     */
    protected $errorMessage = 'This Licensee Term Group does not exist.';

    /**
     * @var string
     */
    protected $idKey = 'groupId';

    /**
     * Repository class
     *
     * @var string
     */
    protected $repoClass = LicenseeTermGroupRepositoryInterface::class;
}
