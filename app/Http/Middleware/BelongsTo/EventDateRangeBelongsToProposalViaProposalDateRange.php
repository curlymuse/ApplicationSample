<?php

namespace App\Http\Middleware\BelongsTo;

use App\Exceptions\Middleware\IncorrectAssociationException;
use App\Repositories\Contracts\ProposalDateRangeRepositoryInterface;
use App\Repositories\Contracts\ProposalRepositoryInterface;
use Closure;

class EventDateRangeBelongsToProposalViaProposalDateRange extends BelongsTo
{

    /**
     * @var ProposalDateRangeRepositoryInterface
     */
    private $dateRangeRepo;

    /**
     * @param ProposalDateRangeRepositoryInterface $dateRangeRepo
     */
    public function __construct(ProposalDateRangeRepositoryInterface $dateRangeRepo)
    {
        $this->dateRangeRepo = $dateRangeRepo;
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
        if (! $this->dateRangeRepo->dateRangeExistsOnProposal(
            $request->route('eventDateRangeId'),
            $request->route('proposalId')
        ))
        {
            throw new IncorrectAssociationException('This event date range does not exist on this proposal.');
        }

        return $next($request);
    }
}
