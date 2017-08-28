<?php

namespace App\Http\Middleware\CanAccess;

use App\Models\ProposalRequest;
use App\Repositories\Contracts\ProposalRequestRepositoryInterface;

class ThisLicenseeCanAccessProposalRequest extends CanAccess
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
     * For authorization logic
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected function isAuthorized($request)
    {
        return $this->requestRepo->belongsToLicensee(
            $request->route('requestId'),
            licenseeId()
        );
    }
}
