<?php

namespace App\Repositories\Contracts;

interface LicenseeRepositoryInterface
{
    /**
     * Attach user to licensee as brand contact
     *
     * @param int $licenseeId
     * @param int $userId
     *
     * @return mixed
     */
    public function attachBrandContact($licenseeId, $userId);

    /**
     * Detach user from licensee as brand contact
     *
     * @param int $licenseeId
     * @param int $userId
     *
     * @return mixed
     */
    public function detachBrandContact($licenseeId, $userId);
}
