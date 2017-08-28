<?php

namespace App\Http\Middleware\ObjectExists;

use App\Repositories\Contracts\RequestQuestionRepositoryInterface;
use Closure;

class RequestQuestionExists extends ObjectExists
{
    /**
     * @var string
     */
    protected $errorMessage = 'This question does not exist.';

    /**
     * @var string
     */
    protected $idKey = 'questionId';

    /**
     * Repository class
     *
     * @var string
     */
    protected $repoClass = RequestQuestionRepositoryInterface::class;
}
