<?php

namespace App\Http\Middleware;

use App\Exceptions\Middleware\NotAuthorizedException;
use App\Repositories\Contracts\ProposalRepositoryInterface;
use App\Repositories\Contracts\RequestHotelRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Auth;
use Closure;

/**
 * Class ProposalAuthOrQueryString
 *
 * This middleware is designed for a front-end Proposal page for a hotel user. It allows
 * for an authenticated user with access privileges to the Proposal to pass through, or
 * a user with a query string hash that gives them non-authenticated access. Furthermore,
 * if an authenticated user attempts to access the page using a query string hash and they
 * DO have access to the proposal, the page will reload without the query string.
 *
 * @package App\Http\Middleware
 */
class ProposalAuthOrQueryString
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
        $intendedRoute = $request->route()->getName();

        //  Pull the query string keys from config
        $userKey = config('resbeat.urls.query-string-user-key');
        $hashKey = config('resbeat.urls.query-string-hash-key');

        $hasQueryStringParams = $request->has($userKey) && $request->has($hashKey);

        if ($user = Auth::user()) {

            //  Make sure the user can access this proposal request.
            if ($this->requestHotelRepo->userCanAccessProposal(
                $user->id,
                $request->route('proposalId')
            )) {
                //  If there are hash string params, redirect to non-query-string version of the page
                if ($hasQueryStringParams) {
                    return redirect()->route($intendedRoute, $request->route('proposalId'));
                }

                //  If there are no hash params, then we're good - let the page load normally
                return $next($request);
            }

            //  If we are here, the user does not have access to the proposal request, so throw an error
            throw new NotAuthorizedException('You are not authorized to view this proposal.');
        }

        //  If we are this far, there is no authenticated user, so we need to validate using the query string

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
