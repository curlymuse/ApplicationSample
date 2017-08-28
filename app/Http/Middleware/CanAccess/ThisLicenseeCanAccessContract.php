<?php

namespace App\Http\Middleware\CanAccess;


use App\Repositories\Contracts\ContractRepositoryInterface;

class ThisLicenseeCanAccessContract extends CanAccess
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
     * For authorization logic
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected function isAuthorized($request)
    {
        return $this->contractRepo->belongsToLicensee(
            $request->route('contractId'),
            licenseeId()
        );
    }
}
