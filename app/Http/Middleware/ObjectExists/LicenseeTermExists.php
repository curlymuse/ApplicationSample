<?php

namespace App\Http\Middleware\ObjectExists;

use App\Repositories\Contracts\LicenseeTermRepositoryInterface;
use Closure;

class LicenseeTermExists extends ObjectExists
{
    /**
     * @var string
     */
    protected $errorMessage = 'This Licensee Term does not exist.';

    /**
     * @var string
     */
    protected $idKey = 'termId';

    /**
     * Repository class
     *
     * @var string
     */
    protected $repoClass = LicenseeTermRepositoryInterface::class;
}
