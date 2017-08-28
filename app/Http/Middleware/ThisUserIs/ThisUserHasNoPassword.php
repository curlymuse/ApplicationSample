<?php

namespace App\Http\Middleware\ThisUserIs;

use App\Exceptions\Middleware\NotAuthorizedException;
use App\Http\Middleware\ApiMiddleware;
use App\Repositories\Contracts\UserRepositoryInterface;
use Closure;

class ThisUserHasNoPassword
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepo;

    /**
     * UserHasPassword constructor.
     * @param UserRepositoryInterface $userRepo
     */
    public function __construct(UserRepositoryInterface $userRepo)
    {
        $this->userRepo = $userRepo;
    }

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
        $user = userFromAuthOrQueryString();

        if ($user->password) {
            throw new NotAuthorizedException('Only new users with no password can access this.');
        }

        return $next($request);
    }
}
