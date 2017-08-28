<?php

namespace App\Repositories\Contracts;

interface SpaceRequestRepositoryInterface
{
    /**
     * Sync space requests for event date range
     *
     * @param int $proposalRequestId
     * @param int $eventDateRangeId
     * @param array $spaces
     * @param string $type
     *
     * @return mixed
     */
    public function syncForEventDateRange($proposalRequestId, $eventDateRangeId, $spaces = [], $type = 'Meeting');
}
