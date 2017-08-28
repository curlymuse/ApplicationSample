<?php

namespace App\Http\Middleware\ObjectExists;

use App\Exceptions\Middleware\InvalidObjectException;
use App\Repositories\Eloquent\Repository;
use Closure;

abstract class ObjectExists
{
    /**
     * Eloquent Repository
     *
     * @var \App\Repositories\Eloquent\Repository
     */
    protected $repo;

    /**
     * The key in Request in which the key is stored
     *
     * @var string
     */
    protected $idKey = 'id';

    /**
     * Class of the repository
     *
     * @var string
     */
    protected $repoClass;

    /**
     * The class of the exception to be thrown if exists() fails
     *
     * @var string
     */
    protected $exceptionClass = InvalidObjectException::class;

    /**
     * The text of the error to be returned with the exception
     *
     * @var string
     */
    protected $errorMessage = 'This object does not exist.';

    /**
     * Instantiate the repository
     */
    public function __construct()
    {
        $this->repo = app($this->repoClass);
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! $this->repo->exists($request->route($this->idKey))) {
            throw new $this->exceptionClass($this->errorMessage);
        }

        return $next($request);
    }
}
