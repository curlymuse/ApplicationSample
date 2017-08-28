<?php

namespace App\Http\Middleware\ThisUserIs;

use App\Exceptions\Middleware\NotAuthorizedException;
use App\Repositories\Contracts\UserRepositoryInterface;
use Closure;

class ThisUserIsHotelUser
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepo;

    /**
     * UserIsHotelUser constructor.
     *
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
     *
     * @return mixed
     *
     * @throws NotAuthorizedException
     */
    public function handle($request, Closure $next)
    {
        if (! userId() || ! $this->userRepo->isHotelUser(userId())) {
            throw new NotAuthorizedException('You must be logged in as a Licensee to access this.');
        }

        return $next($request);
    }
}
