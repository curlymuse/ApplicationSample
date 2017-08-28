<?php

namespace App\Repositories\Eloquent;

use App\Models\Contract;
use App\Repositories\Contracts\ChangeOrderRepositoryInterface;
use Carbon\Carbon;

class ChangeOrderRepository extends Repository implements ChangeOrderRepositoryInterface
{
    /**
     * What columns can be supplied as arguments for a change order
     *
     * @var array
     */
    private static $changeableFields = [
        'commission',
        'rebate',
        'additional_charge_per_adult',
        'tax_rate',
        'min_age_to_check_in',
        'additional_fees',
        'additional_fees_units',
        'deposit_policy',
        'cancellation_policy',
        'cancellation_policy_days',
        'cancellation_policy_file',
        'questions',
        'meeting_spaces',
        'food_and_beverage',
        'cutoff_date',
        'attrition_rate',
        'notes',
        'rooms',
    ];

    /**
     * What arguments should be processed by looking up and updating a value in
     * that column's JSON object
     *
     * @var array
     */
    private static $jsonColumns = [
        'questions',
        'meeting_spaces',
        'food_and_beverage',
        'notes',
    ];

    /**
     * Get all change orders for this Contract
     *
     * @param int $contractId
     *
     * @return mixed
     */
    public function allForContract($contractId)
    {
        return $this->model
            ->whereContractId($contractId)
            ->whereNull('parent_id')
            ->get();
    }

    /**
     * Create a parent change order and nest child items under it for all changes
     *
     * @param int $contractId
     * @param int $userId
     * @param string $userType
     * @param array $changeData
     * @param array $labels
     * @param string|null $reason
     *
     * @return mixed
     */
    public function createSet($contractId, $userId, $userType, $changeData, $labels = [], $reason = null)
    {
        $parent = $this->store([
            'reason'    => $reason,
            'contract_id'   => $contractId,
            'initiated_by_user' => $userId,
            'initiated_by_party' => $userType,
        ]);

        foreach ($changeData as $key => $info) {
            $this->store([
                'contract_id'   => $contractId,
                'initiated_by_user' => $userId,
                'initiated_by_party' => $userType,
                'parent_id' => $parent->id,
                'change_key'    => $info['key'],
                'change_display'    => (isset($labels[$info['key']])) ? $labels[$info['key']] : $info['key'],
                'original_value'  => (isset($info['original'])) ? $info['original'] : null,
                'proposed_value'  => (isset($info['proposed'])) ? $info['proposed'] : null,
                'change_type'     => $info['type'],
            ]);
        }

        return $parent;
    }

    /**
     * Decline a change order item
     *
     * @param int $changeOrderId
     * @param int $userId
     * @param string $reason
     *
     * @return mixed
     */
    public function decline($changeOrderId, $userId, $reason)
    {
        $this->update(
            $changeOrderId,
            [
                'declined_by_user'  => $userId,
                'declined_at'   => Carbon::now(),
                'declined_because'  => $reason,
            ]
        );
    }

    /**
     * Accept a change order item and update the contract accordingly
     *
     * @param int $changeOrderId
     * @param int $userId
     *
     * @return mixed
     */
    public function accept($changeOrderId, $userId)
    {
        $this->update(
            $changeOrderId,
            [
                'accepted_by_user'  => $userId,
                'accepted_at'   => Carbon::now(),
            ]
        );
    }

    /**
     * @return array
     */
    public static function getChangeableFields()
    {
        return self::$changeableFields;
    }

    /**
     * @return array
     */
    public static function getJsonColumns()
    {
        return self::$jsonColumns;
    }

}