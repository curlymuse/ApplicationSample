<?php

namespace App\Http\Middleware\CanAccess;


use App\Repositories\Contracts\ClauseRepositoryInterface;

class ThisLicenseeCanAccessClause extends CanAccess
{

    /**
     * @var ClauseRepositoryInterface
     */
    private $clauseRepo;

    /**
     * @param ClauseRepositoryInterface $clauseRepo
     */
    public function __construct(ClauseRepositoryInterface $clauseRepo)
    {
        $this->clauseRepo = $clauseRepo;
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
        return $this->clauseRepo->belongsToLicensee(
            $request->route('clauseId'),
            licenseeId()
        );
    }
}
