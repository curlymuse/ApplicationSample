<?php

namespace App\Repositories\Contracts;

interface LicenseeTermRepositoryInterface
{
    /**
     * Create a new term for this group
     *
     * @param int $groupId
     * @param string $title
     * @param string $description
     *
     * @return mixed
     */
    public function storeForTermGroup($groupId, $title, $description);
}