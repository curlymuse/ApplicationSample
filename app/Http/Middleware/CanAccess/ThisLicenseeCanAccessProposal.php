<?php

namespace App\Http\Middleware\CanAccess;


use App\Repositories\Contracts\ProposalRepositoryInterface;

class ThisLicenseeCanAccessProposal extends CanAccess
{

    /**
     * @var ProposalRepositoryInterface
     */
    private $proposalRepo;

    /**
     * @param ProposalRepositoryInterface $proposalRepo
     */
    public function __construct(ProposalRepositoryInterface $proposalRepo)
    {
        $this->proposalRepo = $proposalRepo;
    }

    /**
     * For authorization logic
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected function isAuthorized($request)
    {
        return $this->proposalRepo->belongsToLicensee(
            $request->route('proposalId'),
            licenseeId()
        );
    }
}
