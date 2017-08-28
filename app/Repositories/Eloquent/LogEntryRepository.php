<?php

namespace App\Repositories\Eloquent;

use App\Models\Contract;
use App\Models\LogEntry;
use App\Models\Proposal;
use App\Models\ProposalRequest;
use App\Models\User;
use App\Repositories\Contracts\LogEntryRepositoryInterface;

class LogEntryRepository extends Repository implements LogEntryRepositoryInterface
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
    )
    {
        $return = collect();

        $query = $this->model
            ->whereAccountType($accountType)
            ->whereAccountId($accountId);

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        if ($typeFilter) {
            $query->whereAction($typeFilter);
        }

        $results = $query->get();

        foreach ($results->pluck('action')->unique() as $action) {
            $return[$action] = $results->where('action', $action)->all();
        }

        return $return;
    }

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
    public function emailLoggedForUserAndSubject($userId, $mailableClass, $subjectType, $subjectId)
    {
        return $this->model
            ->whereUserId($userId)
            ->whereAccountType(User::class)
            ->whereAccountId($userId)
            ->whereAction('mail:sent')
            ->whereSubjectType($subjectType)
            ->whereSubjectId($subjectId)
            ->whereNotes($mailableClass)
            ->exists();
    }

    /**
     * All activities for a proposal
     *
     * @param int $proposalId
     *
     * @return mixed
     */
    public function allActivitiesForProposal($proposalId)
    {
        return $this->model
            ->where('action', '!=', 'mail:sent')
            ->where(function($query) use ($proposalId) {
                $query
                    ->where(function ($query) use ($proposalId) {
                        $query->whereSubjectType(Proposal::class)
                            ->whereSubjectId($proposalId);
                    })
                    ->orWhereExists(function ($query) use ($proposalId) {
                        $query->select(\DB::raw(1))
                            ->from('proposals')
                            ->where(\DB::raw('proposals.proposal_request_id'), \DB::raw('log_entries.subject_id'))
                            ->where(\DB::raw('log_entries.subject_type'), ProposalRequest::class);
                    });
            })->get();
    }

    /**
     * All activities for a contract
     *
     * @param int $proposalId
     *
     * @return mixed
     */
    public function allActivitiesForContract($contractId)
    {
        return $this->model
            ->whereSubjectType(Contract::class)
            ->whereSubjectId($contractId)
            ->get();
    }
}