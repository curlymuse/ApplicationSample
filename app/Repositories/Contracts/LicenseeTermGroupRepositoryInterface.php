<?php

namespace App\Repositories\Contracts;

interface LicenseeTermGroupRepositoryInterface
{
    /**
     * Get all term groups for this licensee
     *
     * @param int $licenseeId
     *
     * @return mixed
     */
    public function allForLicensee($licenseeId);

    /**
     * Create a new term group for this licensee
     *
     * @param int $licenseeId
     * @param string $name
     *
     * @return mixed
     */
    public function storeForLicensee($licenseeId, $name);

    /**
     * Does this group belong to this licensee?
     *
     * @param int $groupId
     * @param int $licenseeId
     *
     * @return mixed
     */
    public function belongsToLicensee($groupId, $licenseeId);
}