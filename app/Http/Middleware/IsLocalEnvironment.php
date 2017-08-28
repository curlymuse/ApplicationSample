<?php

namespace App\Http\Middleware;

use App;
use App\Exceptions\Middleware\IncompatibleEnvironmentException;
use Closure;

class IsLocalEnvironment
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (App::environment() != 'local') {
            throw new IncompatibleEnvironmentException(
                'This route can only be accessed in a local environment.'
            );
        }

        return $next($request);
    }
}
