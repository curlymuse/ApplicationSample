<?php

namespace App\Http\Middleware\BelongsTo;

use App\Exceptions\Middleware\IncorrectAssociationException;
use App\Repositories\Contracts\ProposalRequestRepositoryInterface;
use Closure;

class EventDateRangeBelongsToProposalRequest
{
    /**
     * @var ProposalRequestRepositoryInterface
     */
    private $requestRepo;

    /**
     * @param ProposalRequestRepositoryInterface $requestRepo
     */
    public function __construct(ProposalRequestRepositoryInterface $requestRepo)
    {
        $this->requestRepo = $requestRepo;
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
        $proposalRequest = $this->requestRepo->find($request->route('requestId'));

        if (! $proposalRequest->event->dateRanges->pluck('id')->contains($request->route('eventDateRangeId'))) {
            throw new IncorrectAssociationException(
                'This Date Range does not belong to the Proposal Request.'
            );
        }

        return $next($request);
    }
}
