<?php

namespace App\Repositories\Contracts;

interface TagRepositoryInterface
{
    /**
     * Accept an array of strings, insert the new ones, and pull IDs for both new and existing
     *
     * @param array $tagStrings
     *
     * @return array
     */
    public function exchangeForIds($tagStrings = []);

    /**
     * Get all tags for this licensee
     *
     * @param int $licenseeId
     *
     * @return mixed
     */
    public function allForLicensee($licenseeId);
}