<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\LicenseeTermGroupRepositoryInterface;

class LicenseeTermGroupRepository extends Repository implements LicenseeTermGroupRepositoryInterface
{
    /**
     * Get all term groups for this licensee
     *
     * @param int $licenseeId
     *
     * @return mixed
     */
    public function allForLicensee($licenseeId)
    {
        return $this->model
            ->whereLicenseeId($licenseeId)
            ->with('terms')
            ->get();
    }

    /**
     * Create a new term group for this licensee
     *
     * @param int $licenseeId
     * @param string $name
     *
     * @return mixed
     */
    public function storeForLicensee($licenseeId, $name)
    {
        return $this->store([
            'licensee_id'   => $licenseeId,
            'name'          => $name
        ]);
    }

    /**
     * Does this group belong to this licensee?
     *
     * @param int $groupId
     * @param int $licenseeId
     *
     * @return mixed
     */
    public function belongsToLicensee($groupId, $licenseeId)
    {
        return $this->model
            ->whereId($groupId)
            ->whereLicenseeId($licenseeId)
            ->exists();
    }
}