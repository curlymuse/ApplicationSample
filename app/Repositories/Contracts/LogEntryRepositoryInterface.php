<?php

namespace App\Repositories\Contracts;

interface LogEntryRepositoryInterface
{
    /**
     * Get an indexed catalog of log entries for an account
     *
     * @param string $accountType
     * @param int $accountId
     * @param string|null $startDate
     * @param string|null $endDate
     * @param string|null $typeFilter
     *
     * @return mixed
     */
    public function getCatalogForAccountAndDates(
        $accountType,
        $accountId,
        $startDate = null,
        $endDate = null,
        $typeFilter = null
    );

    /**
     * All activities for a proposal
     *
     * @param int $proposalId
     *
     * @return mixed
     */
    public function allActivitiesForProposal($proposalId);

    /**
     * All activities for a contract
     *
     * @param int $proposalId
     *
     * @return mixed
     */
    public function allActivitiesForContract($contractId);

    /**
     * Has an outgoing email been logged for this user, this subject, this mailer class?
     *
     * @param int $userId
     * @param string $mailableClass
     * @param string $subjectType
     * @param int $subjectId
     *
     * @return bool
     */
    public function emailLoggedForUserAndSubject($userId, $mailableClass, $subjectType, $subjectId);
}