<?php

namespace App\Repositories\Contracts;

interface ProposalActionRepositoryInterface
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
    public function logAction($userId, $proposalId, $action, $notes = null);
}