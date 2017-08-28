<?php

namespace App\Http\Middleware\ObjectExists;

use App\Repositories\Contracts\LicenseeQuestionGroupRepositoryInterface;
use Closure;

class LicenseeQuestionGroupExists extends ObjectExists
{
    /**
     * @var string
     */
    protected $errorMessage = 'This question group does not exist.';

    /**
     * @var string
     */
    protected $idKey = 'groupId';

    /**
     * Repository class
     *
     * @var string
     */
    protected $repoClass = LicenseeQuestionGroupRepositoryInterface::class;
}
