<?php

namespace App\Http\Middleware\BelongsTo;

use App\Exceptions\Middleware\IncorrectAssociationException;
use App\Repositories\Contracts\ContractRepositoryInterface;
use Closure;

class ContractBelongsToProposalRequest
{

    /**
     * @var ContractRepositoryInterface
     */
    private $contractRepo;

    /**
     * @param ContractRepositoryInterface $contractRepo
     */
    public function __construct(ContractRepositoryInterface $contractRepo)
    {
        $this->contractRepo = $contractRepo;
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
        if (! $this->contractRepo->belongsToProposalRequest(
            $request->route('contractId'),
            $request->route('requestId')
        )) {
            throw new IncorrectAssociationException('This Contract does not belong to this Proposal Request.');
        }

        return $next($request);
    }
}
