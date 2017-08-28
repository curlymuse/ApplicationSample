<?php

namespace App\Repositories\Contracts;

interface LicenseeQuestionGroupRepositoryInterface
{
    /**
     * Get all groups for licensee
     *
     * @param int $licenseeId
     *
     * @return mixed
     */
    public function allForLicensee($licenseeId);

    /**
     * Create new QuestionGRoup for Licensee
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