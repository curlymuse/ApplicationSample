<?php

namespace App\Repositories\Contracts;

use App\Models\Contract;

interface ChangeOrderRepositoryInterface
{
    /**
     * Get all change orders for this Contract
     *
     * @param int $contractId
     *
     * @return mixed
     */
    public function allForContract($contractId);

    /**
     * Decline a change order item
     *
     * @param int $changeOrderId
     * @param int $userId
     * @param string $reason
     *
     * @return mixed
     */
    public function decline($changeOrderId, $userId, $reason);

    /**
     * Accept a change order item and update the contract accordingly
     *
     * @param int $changeOrderId
     * @param int $userId
     *
     * @return mixed
     */
    public function accept($changeOrderId, $userId);

    /**
     * Create a parent change order and nest child items under it for all changes
     *
     * @param int $contractId
     * @param int $userId
     * @param string $userType
     * @param array $changeData
     * @param array $labels
     *
     * @return mixed
     */
    public function createSet($contractId, $userId, $userType, $changeData, $labels = []);
}