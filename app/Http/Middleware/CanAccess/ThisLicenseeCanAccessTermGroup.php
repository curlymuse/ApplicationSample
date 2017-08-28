<?php

namespace App\Http\Middleware\CanAccess;


use App\Repositories\Contracts\LicenseeTermGroupRepositoryInterface;

class ThisLicenseeCanAccessTermGroup extends CanAccess
{

    /**
     * @var LicenseeTermGroupRepositoryInterface
     */
    private $groupRepo;

    /**
     * @param LicenseeTermGroupRepositoryInterface $groupRepo
     */
    public function __construct(LicenseeTermGroupRepositoryInterface $groupRepo)
    {
        $this->groupRepo = $groupRepo;
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
        return $this->groupRepo->belongsToLicensee(
            $request->route('groupId'),
            licenseeId()
        );
    }
}
