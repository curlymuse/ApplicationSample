<?php

namespace App\Http\Middleware\CanAccess;

use App\Exceptions\Middleware\NotAuthorizedException;
use Closure;

abstract class CanAccess
{
    /**
     * @var string
     */
    protected $errorMessage = 'You are not authorized to access this object.';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     * @throws NotAuthorizedException
     */
    public function handle($request, Closure $next)
    {
        if (! $this->isAuthorized($request)) {
            throw new NotAuthorizedException($this->errorMessage);
        }

        return $next($request);
    }

    /**
     * For authorization logic
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    abstract protected function isAuthorized($request);
}
