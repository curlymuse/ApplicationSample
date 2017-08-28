<?php

namespace App\Repositories\Contracts;

interface RequestQuestionRepositoryInterface
{
    /**
     * Get an array that links ID to question string for all passed-in IDs
     *
     * @param array $ids
     *
     * @return mixed
     */
    public function getLookupTable($ids = []);

    /**
     * Take an array of questionID/answer combinations, and convert it to an array
     * of question/answer combinations
     *
     * @param array $questionItems
     *
     * @return mixed
     */
    public function injectQuestionText($questionItems);

    /**
     * Add new question to this group
     *
     * @param int $groupId
     * @param string $questionText
     *
     * @return mixed
     */
    public function storeForGroup($groupId, $questionText);
}