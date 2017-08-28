<?php

namespace App\Repositories\Contracts;

interface ContractTermRepositoryInterface
{
    /**
     * Create a term for this group
     *
     * @param int $groupId
     * @param string $title
     * @param string $description
     *
     * @return mixed
     */
    public function storeForGroup($groupId, $title, $description);
}