<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\RequestQuestionGroupRepositoryInterface;

class RequestQuestionGroupRepository extends Repository implements RequestQuestionGroupRepositoryInterface
{
    /**
     * Get all question groups for this proposal request
     *
     * @param int $requestId
     *
     * @return mixed
     */
    public function allForProposalRequest($requestId)
    {
        return $this->model
            ->whereProposalRequestId($requestId)
            ->with('questions')
            ->get();
    }

    /**
     * Create new question group for proposal request
     *
     * @param int $requestId
     * @param string $name
     *
     * @return mixed
     */
    public function storeForProposalRequest($requestId, $name)
    {
        return $this->store([
            'proposal_request_id'       => $requestId,
            'name'                      => $name,
        ]);
    }

    /**
     * Delete question group and all questions in it
     *
     * @param int $groupId
     *
     * @return mixed
     */
    public function deleteGroupAndChildren($groupId)
    {
        foreach ($this->find($groupId)->questions as $question) {
            $question->delete();
        }

        $this->delete($groupId);
    }
}