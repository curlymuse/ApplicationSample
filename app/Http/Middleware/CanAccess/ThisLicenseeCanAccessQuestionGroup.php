<?php

namespace App\Http\Middleware\CanAccess;


use App\Repositories\Contracts\LicenseeQuestionGroupRepositoryInterface;

class ThisLicenseeCanAccessQuestionGroup extends CanAccess
{

    /**
     * @var LicenseeQuestionGroupRepositoryInterface
     */
    private $groupRepo;

    /**
     * @param LicenseeQuestionGroupRepositoryInterface $groupRepo
     */
    public function __construct(LicenseeQuestionGroupRepositoryInterface $groupRepo)
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
