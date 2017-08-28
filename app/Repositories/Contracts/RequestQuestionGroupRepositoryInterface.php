<?php

namespace App\Repositories\Contracts;

interface RequestQuestionGroupRepositoryInterface
{
    /**
     * Get all question groups for this proposal request
     *
     * @param int $requestId
     *
     * @return mixed
     */
    public function allForProposalRequest($requestId);

    /**
     * Create new question group for proposal request
     *
     * @param int $requestId
     * @param string $name
     *
     * @return mixed
     */
    public function storeForProposalRequest($requestId, $name);

    /**
     * Delete question group and all questions in it
     *
     * @param int $groupId
     *
     * @return mixed
     */
    public function deleteGroupAndChildren($groupId);
}