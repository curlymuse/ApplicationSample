<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\ContractTermRepositoryInterface;

class ContractTermRepository extends Repository implements ContractTermRepositoryInterface
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
    public function storeForGroup($groupId, $title, $description)
    {
        return $this->store([
            'contract_term_group_id'    => $groupId,
            'title'               => $title,
            'description'               => $description,
        ]);
    }
}