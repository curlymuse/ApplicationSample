<?php

namespace App\Support;

use App\Repositories\Contracts\ContractRepositoryInterface;

class ChangeOrderParser
{
    /**
     * What columns can be supplied as arguments for a change order
     *
     * @var array
     */
    private static $changeableFields = [
        'start_date',
        'end_date',
        'check_in_date',
        'check_out_date',
        'is_meeting_space_required',
        'is_food_and_beverage_required',
        'commission',
        'rebate',
        'additional_charge_per_adult',
        'tax_rate',
        'min_age_to_check_in',
        'min_length_of_stay',
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
        'term_groups',
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
    ];

    /**
     * @var ContractRepositoryInterface
     */
    private $contractRepo;

    /**
     * @var \App\Models\Contract
     */
    protected $contract;

    /**
     * @var array
     */
    protected $rawInputData = [];

    /**
     * @var array
     */
    protected $inputData = [];

    /**
     * @var array
     */
    protected $contractData = [];

    /**
     * @var array
     */
    protected $addAttachments = [];

    /**
     * @var array
     */
    protected $removeAttachments = [];

    /**
     * ChangeOrderParser constructor.
     * @param ContractRepositoryInterface $contractRepo
     */
    public function __construct(ContractRepositoryInterface $contractRepo)
    {
        $this->contractRepo = $contractRepo;
    }

    /**
     * Parse differences between contract and input data into individual changes
     *
     * @param int $contractId
     * @param $rawInputData
     * @param array $addAttachments
     * @param array $removeAttachments
     *
     * @return mixed
     */
    public function parseChanges($contractId, $rawInputData, $addAttachments = [], $removeAttachments = [])
    {
        $this->contract = $this->contractRepo->find($contractId);
        $this->rawInputData = $rawInputData;
        $this->inputData = $this->parseInputData($rawInputData);
        $this->contractData = $this->parseExistingContract($this->contract);
        $this->addAttachments = $addAttachments;
        $this->removeAttachments = $removeAttachments;

        return $this->diff();
    }

    /**
     * Parse Contract data
     *
     * @param \App\Models\Contract $contract
     *
     * @return array
     */
    private function parseExistingContract($contract)
    {
        $existingData = collect($contract)
            ->only(
                self::$changeableFields
            )->merge([
                'rooms'     => $contract->roomSets->map(function($roomSet) {
                    return collect($roomSet)
                        ->only(['id', 'name', 'description', 'rate'])
                        ->merge([
                            'date'      => $roomSet->reservation_date->format('Y-m-d'),
                            'rooms'     => $roomSet->rooms_offered,
                        ])->toArray();
                }),
                'term_groups'     => $contract->termGroups->map(function($group) {
                    return collect($group)
                        ->only(['id', 'name'])
                        ->merge([
                            'terms' => $group->terms->map(function($term) {
                                return collect($term)
                                    ->only(['id', 'description', 'title'])
                                    ->toArray();
                            }),
                        ])
                        ->toArray();
                }),
            ])->toArray();
        $existingData = json_decode_keys(
            $existingData,
            self::$jsonColumns
        );

        return array_dot_deep($existingData);
    }

    /**
     * Parse passed-in input data from front-end
     *
     * @param array $rawInputData
     * @param array $addAttachments
     * @param array $removeAttachments
     *
     * @return array
     */
    private function parseInputData($rawInputData)
    {
        return array_dot_deep(
            collect($rawInputData)->only(
                self::$changeableFields
            )->toArray()
        );
    }

    /**
     * Compare the two sets, and construct a set of change order data items
     *
     * @return mixed
     */
    private function diff()
    {
        $changes = [];

        $this->diffModifications($changes)
            ->diffTermGroups($changes)
            ->diffJsonRemovalsAndModifications($changes)
            ->diffRoomRemovalsAndModifications($changes)
            ->diffJsonAdditions($changes)
            ->diffTermGroupAdditions($changes)
            ->diffRoomAdditions($changes)
            ->diffReservationMethodAdditions($changes)
            ->diffReservationMethodRemovals($changes)
            ->diffPaymentMethodAdditions($changes)
            ->diffPaymentMethodRemovals($changes)
            ->diffAddAttachments($changes)
            ->diffRemoveAttachments($changes);

        return $changes;
    }

    private function diffRoomAdditions(&$changes)
    {
        if (! isset($this->rawInputData['rooms'])) {
            return $this;
        }

        foreach ($this->rawInputData['rooms'] as $room) {
            if (! isset($room['id'])) {
                $changes[] = [
                    'type'  => 'add',
                    'key'   => 'rooms',
                    'proposed'  => json_encode($room),
                ];
            }
        }

        return $this;
    }

    private function diffReservationMethodAdditions(&$changes)
    {
        $existingIds = $this->contract->reservationMethods()->pluck('reservation_methods.id');

        $additions = collect($this->rawInputData['reservation_methods'])->reject(function($id) use ($existingIds) {
            return $existingIds->contains($id);
        })->toArray();

        foreach ($additions as $id) {
            $changes[] = [
                'type'  => 'add',
                'key'   => 'reservation_methods',
                'proposed'  => $id,
            ];
        }

        return $this;
    }

    private function diffPaymentMethodAdditions(&$changes)
    {
        $existingIds = $this->contract->paymentMethods()->pluck('payment_methods.id');

        $additions = collect($this->rawInputData['payment_methods'])->reject(function($id) use ($existingIds) {
            return $existingIds->contains($id);
        })->toArray();

        foreach ($additions as $id) {
            $changes[] = [
                'type'  => 'add',
                'key'   => 'payment_methods',
                'proposed'  => $id,
            ];
        }

        return $this;
    }

    private function diffJsonAdditions(&$changes)
    {
        foreach (self::$jsonColumns as $column) {

            if (! isset($this->rawInputData[$column])) {
                continue;
            }

            foreach ($this->rawInputData[$column] as $item) {
                if (isset($item['index'])) {
                    continue;
                }
                $changes[] = [
                    'type' => 'add',
                    'key' => $column,
                    'proposed' => json_encode($item),
                ];
            }
        }

        return $this;
    }

    private function diffTermGroupAdditions(&$changes)
    {
        if (! isset($this->rawInputData['term_groups'])) {
            return $this;
        }

        foreach ($this->rawInputData['term_groups'] as $group) {

            //  If this is a new group, add the new group in its entirety
            if (! isset($group['id'])) {
                $changes[] = [
                    'type'  => 'add',
                    'key'   => 'term_groups',
                    'proposed'  => json_encode($group),
                ];
                continue;
            }

            // If this an existing group, check for newly added terms
            foreach ($group['terms'] as $term) {
                if (! isset($term['id'])) {
                    $changes[] = [
                        'type'  => 'add',
                        'key'   => sprintf(
                            'term_groups.id:%d.terms',
                            $group['id']
                        ),
                        'proposed'  => json_encode($term),
                    ];
                }
            }
        }

        return $this;
    }

    private function diffReservationMethodRemovals(&$changes)
    {
        $submittedIds = $this->rawInputData['reservation_methods'];

        $removeIds = $this->contract
            ->reservationMethods()
            ->pluck('reservation_methods.id')
            ->reject(function($id) use ($submittedIds) {
                return in_array($id, $submittedIds);
            })->toArray();

        foreach ($removeIds as $id) {
            $changes[] = [
                'type'  => 'remove',
                'key'   => sprintf(
                    'reservation_methods.id:%d',
                    $id
                )
            ];
        }

        return $this;
    }

    private function diffPaymentMethodRemovals(&$changes)
    {
        $submittedIds = $this->rawInputData['payment_methods'];

        $removeIds = $this->contract
            ->paymentMethods()
            ->pluck('payment_methods.id')
            ->reject(function($id) use ($submittedIds) {
                return in_array($id, $submittedIds);
            })->toArray();

        foreach ($removeIds as $id) {
            $changes[] = [
                'type'  => 'remove',
                'key'   => sprintf(
                    'payment_methods.id:%d',
                    $id
                )
            ];
        }

        return $this;
    }

    private function diffRoomRemovalsAndModifications(&$changes)
    {
        if (! isset($this->rawInputData['rooms'])) {
            return $this;
        }

        $inputRoomSetIds = collect($this->rawInputData['rooms'])->pluck('id');

        foreach ($this->contract->roomSets as $room) {

            $roomData = collect($room)->only([
                'id', 'rate', 'description', 'name'
            ])->merge([
                'rooms' => $room->rooms_offered,
                'date'  => $room->reservation_date->format('Y-m-d'),
            ])->toArray();

            //  If this ID does not exist in input data, this is a REMOVAL
            if (!$inputRoomSetIds->contains($roomData['id'])) {
                $changes[] = [
                    'type'  => 'remove',
                    'key'   => sprintf(
                        'rooms.id:%d',
                        $roomData['id']
                    ),
                ];
                continue;
            }

            //  Check for any changes
            $roomInputDataItem = collect($this->rawInputData['rooms'])->where('id', $roomData['id'])->first();
            foreach ($roomData as $key => $value) {
                if ($key == 'id') {
                    continue;
                }

                if ($roomInputDataItem[$key] != $value) {
                    $changes[] = [
                        'type'  => 'modify',
                        'key'   => sprintf(
                            'rooms.id:%d.%s',
                            $roomData['id'],
                            $key
                        ),
                        'original'  => $value,
                        'proposed'  => $roomInputDataItem[$key],
                    ];
                }
            }
        }

        return $this;
    }

    private function diffJsonRemovalsAndModifications(&$changes)
    {
        foreach (self::$jsonColumns as $column) {

            if (! isset($this->rawInputData[$column])) {
                continue;
            }

            $data = json_decode($this->contract->$column);

            foreach ($data as $row) {

                $inputRow = collect($this->rawInputData[$column])
                    ->filter(function($item) use ($row) {
                        return (isset($item['index']) && $item['index'] == $row->index);
                    })->first();

                //  Check for removal
                if (! $inputRow) {
                    $removeKey = sprintf(
                        '%s.index:%d',
                        $column,
                        $row->index
                    );
                    $changes[] = [
                        'key' => $removeKey,
                        'type' => 'remove',
                    ];
                    continue;
                }

                //  Check for modification
                foreach ($row as $key => $value) {
                    if (! in_array($key, array_keys($inputRow)) || $inputRow[$key] != $value) {
                        $changes[] = [
                            'key'   => sprintf(
                                '%s.index:%d.%s',
                                $column,
                                $row->index,
                                $key
                            ),
                            'type'  => 'modify',
                            'original'  => $value,
                            'proposed'  => (isset($inputRow[$key])) ? $inputRow[$key] : null,
                        ];
                    }
                }
            }
        }
        return $this;
    }

    private function diffTermGroups(&$changes)
    {
        if (! isset($this->rawInputData['term_groups'])) {
            return $this;
        }

        foreach ($this->contract->termGroups as $group) {

            //  Pull the input data item with the same ID
            $groupFromInput = collect($this->rawInputData['term_groups'])
                ->where('id', $group->id)
                ->first();

            //  If it does not exist, this is a REMOVE change order
            if (! $groupFromInput) {
                $changes[] = [
                    'type'  => 'remove',
                    'key'   => sprintf(
                        'term_groups.id:%d',
                        $group->id
                    )
                ];
                continue;
            }

            //  Check for a name change
            if ($groupFromInput['name'] != $group->name) {
                $changes[] = [
                    'type'  => 'modify',
                    'key'   => sprintf(
                        'term_groups.id:%d.name',
                        $group->id
                    ),
                    'original'  => $group->name,
                    'proposed'  => json_encode(['name' => $groupFromInput['name']]),
                ];
            }

            //  Check each of the terms
            foreach ($group->terms as $term) {

                //  Pull the input data version of the term
                $termFromInput = collect($groupFromInput['terms'])
                    ->where('id', $term->id)
                    ->first();

                //  If it does not exist, this is a REMOVE change order
                if (! $termFromInput) {
                    $changes[] = [
                        'type'  => 'remove',
                        'key'   => sprintf(
                            'term_groups.id:%d.terms.id:%d',
                            $group->id,
                            $term->id
                        )
                    ];
                    continue;
                }

                //  Check for description/title changes
                foreach (['title', 'description'] as $field) {
                    if ($termFromInput[$field] != $term->$field) {
                        $changes[] = [
                            'type' => 'modify',
                            'key' => sprintf(
                                'term_groups.id:%d.terms.id:%d.%s',
                                $group->id,
                                $term->id,
                                $field
                            ),
                            'original' => $term->$field,
                            'proposed' => json_encode([$field => $termFromInput[$field]]),
                        ];
                    }
                }
            }
        }

        return $this;
    }

    /**
     * @param $changes
     *
     * @return $this
     */
    private function diffModifications(&$changes)
    {
        foreach ($this->contractData as $key => $value) {

            //  Term group modifications are done separately
            if (preg_match('/^term_groups\.([0-9]+)\.([a-z_]+)([a-zA-Z0-9_\.]*)$/', $key)) {
                continue;
            }
            if (preg_match('/^rooms([a-zA-Z0-9\.\:_]+)$/', $key)) {
                continue;
            }

            $jsonPattern = sprintf(
                '/^(%s)\.([a-zA-Z_\.0-9]+)$/',
                implode('|', self::$jsonColumns)
            );
            if (preg_match($jsonPattern, $key)) {
                continue;
            }

            if (in_array(
                $key,
                [
                    'start_date',
                    'end_date',
                    'check_in_date',
                    'check_out_date',
                ]
            )) {
                $value = str_replace(' 00:00:00', '', $value);
            }

            if (isset($this->inputData[$key]) && ($this->inputData[$key] != $value)) {
                $changes[] = [
                    'key'       => $key,
                    'type'      => 'modify',
                    'original'  => $value,
                    'proposed'  => $this->inputData[$key],
                ];
            }
        }

        return $this;
    }

    private function diffAddAttachments(&$changes)
    {
        foreach ($this->addAttachments as $id) {
            $changes[] = [
                'type'  => 'add',
                'key'   => 'attachments',
                'proposed'   => $id,
            ];
        }

        return $this;
    }

    private function diffRemoveAttachments(&$changes)
    {
        foreach ($this->removeAttachments as $id) {
            $changes[] = [
                'type'  => 'remove',
                'key'   => sprintf(
                    'attachments.id:%d',
                    $id
                ),
            ];
        }

        return $this;
    }
}
