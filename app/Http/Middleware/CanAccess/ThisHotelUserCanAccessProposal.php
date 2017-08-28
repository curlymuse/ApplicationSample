<?php

namespace App\Http\Middleware\CanAccess;

use App\Exceptions\Middleware\NotAuthorizedException;
use App\Repositories\Contracts\ProposalRepositoryInterface;
use App\Repositories\Contracts\RequestHotelRepositoryInterface;
use Auth;
use Closure;

class ThisHotelUserCanAccessProposal
{
    /**
     * @var ProposalRepositoryInterface
     */
    private $proposalRepo;

    /**
     * HotelUserCanAccessProposal constructor.
     * @param ProposalRepositoryInterface $proposalRepo
     */
    public function __construct(ProposalRepositoryInterface $proposalRepo)
    {
        $this->proposalRepo = $proposalRepo;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($user = Auth::user()) {

            //  Make sure the user can access this proposal request.
            if ($this->proposalRepo->userBelongsToHotelOnProposal(
                $user->id,
                $request->route('proposalId')
            )) {
                return $next($request);
            }

            //  If we are here, the user does not have access to the proposal request, so throw an error
            throw new NotAuthorizedException('You are not authorized to view this proposal.');
        }

        throw new NotAuthorizedException('You are not authorized to view this proposal.');
    }

}
