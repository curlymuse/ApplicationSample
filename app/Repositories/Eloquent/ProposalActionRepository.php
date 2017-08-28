<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\ProposalActionRepositoryInterface;

class ProposalActionRepository extends Repository implements ProposalActionRepositoryInterface
{
    /**
     * Log a new user action
     *
     * @param int $userId
     * @param int $proposalId
     * @param string $action
     * @param null $notes
     *
     * @return mixed
     */
    public function logAction($userId, $proposalId, $action, $notes = null)
    {
        return $this->store([
            'user_id'   => $userId,
            'proposal_id'   => $proposalId,
            'action'    => $action,
            'notes'     => $notes,
        ]);
    }
}