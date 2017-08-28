<?php

namespace App\Http\Middleware\ObjectExists;

use App\Repositories\Contracts\LicenseeRepositoryInterface;
use Closure;

class LicenseeExists extends ObjectExists
{
    /**
     * @var string
     */
    protected $errorMessage = 'This Licensee does not exist.';

    /**
     * @var string
     */
    protected $idKey = 'licenseeId';

    /**
     * Repository class
     *
     * @var string
     */
    protected $repoClass = LicenseeRepositoryInterface::class;
}
