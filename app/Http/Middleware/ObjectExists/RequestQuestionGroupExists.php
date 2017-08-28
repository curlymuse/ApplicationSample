<?php

namespace App\Http\Middleware\ObjectExists;

use App\Repositories\Contracts\RequestQuestionGroupRepositoryInterface;
use Closure;

class RequestQuestionGroupExists extends ObjectExists
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
    protected $repoClass = RequestQuestionGroupRepositoryInterface::class;
}
