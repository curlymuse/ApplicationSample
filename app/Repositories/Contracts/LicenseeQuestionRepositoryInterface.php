<?php

namespace App\Repositories\Contracts;

interface LicenseeQuestionRepositoryInterface
{
    /**
     * Create a new question for this group
     *
     * @param int $groupId
     * @param string $questionText
     *
     * @return mixed
     */
    public function storeForGroup($groupId, $questionText);
}