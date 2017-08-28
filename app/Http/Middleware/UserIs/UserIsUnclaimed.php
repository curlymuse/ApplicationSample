<?php

namespace App\Http\Middleware\UserIs;

use App\Exceptions\JobPolicy\InvalidStateException;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Closure;

class UserIsUnclaimed
{
    /*
     *
     */
    /**
     * @var UserRepositoryInterface
     */
    private $userRepo;

    /**
     * UserIsUnclaimed constructor.
     * @param UserRepositoryInterface $userRepo
     */
    public function __construct(UserRepositoryInterface $userRepo)
    {
        $this->userRepo = $userRepo;
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
        $user = $this->userRepo->find($request->route('userId'));

        if ($user->password) {
            throw new InvalidStateException('This user has a claimed account and cannot be edited from here.');
        }

        return $next($request);
    }
}
