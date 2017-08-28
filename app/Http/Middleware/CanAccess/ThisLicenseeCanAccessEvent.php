<?php

namespace App\Http\Middleware\CanAccess;


use App\Repositories\Contracts\EventRepositoryInterface;

class ThisLicenseeCanAccessEvent extends CanAccess
{

    /**
     * @param EventRepositoryInterface $clauseRepo
     */
    public function __construct(EventRepositoryInterface $eventRepo)
    {
        $this->eventRepo = $eventRepo;
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
        return $this->eventRepo->belongsToLicensee(
            $request->route('eventId'),
            licenseeId()
        );
    }
}
