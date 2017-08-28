<?php

namespace App\Http\Middleware;

use App\Exceptions\Middleware\NotAuthorizedException;
use App\Exceptions\MyException;
use App\Repositories\Contracts\ProposalRepositoryInterface;
use App\Repositories\Contracts\RequestHotelRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Auth;
use Closure;

/**
 * Class ProposalRequestQueryString
 *
 * This middleware validates a user/hash query string and ensures that the user
 * specified has access to the proposal whose ID is indicated in the URL
 *
 * @package App\Http\Middleware
 */
class ProposalQueryString
{
    /**
     * @var RequestHotelRepositoryInterface
     */
    private $requestHotelRepo;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepo;

    /**
     * @param UserRepositoryInterface $userRepo
     * @param RequestHotelRepositoryInterface $requestHotelRepo
     */
    public function __construct(
        UserRepositoryInterface $userRepo,
        RequestHotelRepositoryInterface $requestHotelRepo
    )
    {
        $this->requestHotelRepo = $requestHotelRepo;
        $this->userRepo = $userRepo;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param null $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        //  Pull the query string keys from config
        $userKey = config('resbeat.urls.query-string-user-key');
        $hashKey = config('resbeat.urls.query-string-hash-key');

        $hasQueryStringParams = $request->has($userKey) && $request->has($hashKey);

        //  Make sure there is a query string
        if (! $hasQueryStringParams) {
            throw new NotAuthorizedException('You are not authorized to view this proposal.');
        }

        //  Pole for a user that has the supplied ID and hash AND has access to the proposal request
        $user = $this->userRepo->findUsingProposalAndUserHash(
            $request->get($userKey),
            $request->route('proposalId'),
            $request->get($hashKey)
        );

        //  If no such user exists, throw an error
        if (! $user) {
            throw new NotAuthorizedException('You are not authorized to view this proposal.');
        }

        //  If we are this far, no user is logged in, and the hash/userId/requestId combination checks out,
        //  let them through.
        return $next($request);
    }
}
