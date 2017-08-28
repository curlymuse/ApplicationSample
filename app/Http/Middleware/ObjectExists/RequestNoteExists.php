<?php

namespace App\Http\Middleware\ObjectExists;

use App\Repositories\Contracts\RequestNoteRepositoryInterface;
use Closure;

class RequestNoteExists extends ObjectExists
{

    /**
     * @var string
     */
    protected $errorMessage = 'This Request Note does not exist.';

    /**
     * @var string
     */
    protected $idKey = 'noteId';

    /**
     * Repository class
     *
     * @var string
     */
    protected $repoClass = RequestNoteRepositoryInterface::class;
}
