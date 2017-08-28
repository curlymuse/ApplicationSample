<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\LicenseeTermRepositoryInterface;

class LicenseeTermRepository extends Repository implements LicenseeTermRepositoryInterface
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
    public function storeForTermGroup($groupId, $title, $description)
    {
        return $this->store([
            'licensee_term_group_id'    => $groupId,
            'title'                     => $title,
            'description'               => $description,
        ]);
    }
}