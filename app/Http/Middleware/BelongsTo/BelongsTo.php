<?php

namespace App\Http\Middleware\BelongsTo;

use App\Exceptions\InvalidObjectException;
use App\Exceptions\Middleware\IncorrectAssociationException;
use App\Repositories\Eloquent\Repository;
use Closure;

abstract class BelongsTo
{
    /**
     * Eloquent Repository
     *
     * @var \App\Repositories\Eloquent\Repository
     */
    protected $repo;

    /**
     * The belonging object's key
     *
     * @var string
     */
    protected $belongingIdKey = 'id';

    /**
     * The belonged-to object's key
     *
     * @var string
     */
    protected $belongedToIdKey = 'id';

    /**
     * Name of foreign column on object's table
     *
     * @var string
     */
    protected $foreignColumn = 'id';

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
    protected $exceptionClass = IncorrectAssociationException::class;

    /**
     * The text of the error to be returned with the exception
     *
     * @var string
     */
    protected $errorMessage = 'This object has an incorrect association.';

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
        if (! $this->repo->belongsTo(
            $request->route($this->belongingIdKey),
            $this->foreignColumn,
            $request->route($this->belongedToIdKey)
        )) {
            throw new $this->exceptionClass($this->errorMessage);
        }

        return $next($request);
    }
}
