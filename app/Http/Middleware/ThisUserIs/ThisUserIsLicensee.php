<?php

namespace App\Http\Middleware\ThisUserIs;

use App\Exceptions\Middleware\NotAuthorizedException;
use Closure;

class ThisUserIsLicensee
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     *
     * @throws NotAuthorizedException
     */
    public function handle($request, Closure $next)
    {
        if (! licenseeId()) {
            throw new NotAuthorizedException('You must be logged in as a Licensee to access this.');
        }

        return $next($request);
    }
}
