<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\RequestQuestionRepositoryInterface;

class RequestQuestionRepository extends Repository implements RequestQuestionRepositoryInterface
{
    /**
     * Get an array that links ID to question string for all passed-in IDs
     *
     * @param array $ids
     *
     * @return mixed
     */
    public function getLookupTable($ids = [])
    {
        return $this->model
            ->whereIn('id', $ids)
            ->pluck('question', 'id')
            ->toArray();
    }

    /**
     * Take an array of questionID/answer combinations, and convert it to an array
     * of question/answer combinations
     *
     * @param array $questionItems
     *
     * @return mixed
     */
    public function injectQuestionText($questionItems)
    {
        $ids = collect($questionItems)->pluck('id')->toArray();

        $table = $this->model
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        $return = [];
        foreach ($questionItems as $item) {
            $return[] = [
                'id'        => $item->id,
                'group'     => $table[$item->id]->group->name,
                'question'  => $table[$item->id]->question,
                'answer'    => $item->answer,
            ];
        }

        return $return;
    }

    /**
     * Add new question to this group
     *
     * @param int $groupId
     * @param string $questionText
     *
     * @return mixed
     */
    public function storeForGroup($groupId, $questionText)
    {
        return $this->store([
            'request_question_group_id' => $groupId,
            'question'      => $questionText,
        ]);
    }
}