<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\LicenseeQuestionRepositoryInterface;

class LicenseeQuestionRepository extends Repository implements LicenseeQuestionRepositoryInterface
{
    /**
     * Create a new question for this group
     *
     * @param int $groupId
     * @param string $questionText
     *
     * @return mixed
     */
    public function storeForGroup($groupId, $questionText)
    {
        return $this->store([
            'licensee_question_group_id'        => $groupId,
            'question'      => $questionText,
        ]);
    }
}