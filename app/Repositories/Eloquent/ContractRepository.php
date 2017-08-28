<?php

namespace App\Repositories\Eloquent;

use App\Models\Brand;
use App\Models\Proposal;
use App\Models\SpaceRequest;
use App\Repositories\Contracts\ContractRepositoryInterface;
use Carbon\Carbon;

class ContractRepository extends Repository implements ContractRepositoryInterface
{
    private static $transferFromSpaceRequest = [
        'start_time',
        'end_time',
        'name',
        'attendees',
        'budget',
        'budget_units',
        'room_type',
        'layout',
        'requests',
        'equipment',
        'meal',
        'notes',
    ];

    /**
     * Get all Contracts for this Proposal Request
     *
     * @param int $requestId
     *
     * @return mixed
     */
    public function allForProposalRequest($requestId)
    {
        return $this->model
            ->whereHas('proposal', function($query) use ($requestId) {
                $query->whereProposalRequestId($requestId);
            })->get();
    }

    /**
     * Get all Contracts for this Licensee
     *
     * @param int $licenseeId
     *
     * @return mixed
     */
    public function allForLicensee($licenseeId)
    {
        return $this->model
            ->whereHas('proposal', function($query) use ($licenseeId) {
                $query->whereHas('proposalRequest', function($query) use ($licenseeId) {
                    $query->whereHas('event', function($query) use ($licenseeId) {
                        $query->whereLicenseeId($licenseeId);
                    });
                });
            })->get();
    }

    /**
     * Get all Contracts associated with this hotelier user's account
     *
     * @param int $userId
     * @param string|null $startDate
     * @param string|null $endDate
     * @param int|null $proposalRequestId
     *
     * @return mixed
     */
    public function allForHotelier($userId, $startDate = null, $endDate = null, $proposalRequestId = null)
    {
        return $this->model
            ->whereHas('dateRange', function($query) use ($startDate, $endDate)
                {
                    if ($startDate) {
                        $query->where('start_date', '>=', $startDate);
                    }
                    if ($endDate) {
                        $query->where('end_date', '<=', $endDate);
                    }
                })
            ->whereHas('proposal', function($query) use ($userId, $proposalRequestId) {
                if ($proposalRequestId) {
                    $query = $query->whereProposalRequestId($proposalRequestId);
                }
                $query->whereHas('hotel', function ($query) use ($userId) {
                    $query->whereHas('hoteliers', function ($query) use ($userId) {
                        $query->where('users.id', $userId);
                    });
                });
            })->get();
    }

    /**
     * Get all proposal requests belonging to hotels of a GSO's brands
     *
     * @param int $userId
     * @param string|null $startDate
     * @param string|null $endDate
     * @param int|null $proposalRequestId
     *
     * @return mixed
     */
    public function allForGSO($userId, $startDate = null, $endDate = null, $proposalRequestId = null)
    {
         return $this->model
             ->whereHas('dateRange', function($query) use ($startDate, $endDate)
             {
                 if ($startDate) {
                     $query->where('start_date', '>=', $startDate);
                 }
                 if ($endDate) {
                     $query->where('end_date', '<=', $endDate);
                 }
             })
             ->whereHas('proposal', function($query) use ($userId, $proposalRequestId) {
                 if ($proposalRequestId) {
                     $query = $query->whereProposalRequestId($proposalRequestId);
                 }
                 $query->whereHas('hotel', function ($query) use ($userId) {
                     $query->whereHas('brand', function ($query) use ($userId) {
                         $query->whereExists(function ($query) use ($userId) {
                             $query->select(\DB::raw(1))
                                 ->from('role_user')
                                 ->where(\DB::raw('role_user.user_id'), $userId)
                                 ->where(\DB::raw('role_user.rolable_type'), Brand::class)
                                 ->whereRaw('role_user.rolable_id = brands.id');
                         });
                     });
                 });
             })->get();
    }

    /**
     * Does this contract have pending change orders
     *
     * @param int $contractId
     *
     * @return bool
     */
    public function hasPendingChangeOrders($contractId)
    {
        return $this->model
            ->whereId($contractId)
            ->whereHas('changeOrders', function($query) {
                $query->whereNull('parent_id')
                    ->whereHas('children', function($query) {
                        $query->whereNull('declined_at')
                            ->whereNull('accepted_at');
                    });
            })
            ->exists();
    }

    /**
     * Does this contract have change orders?
     *
     * @param int $contractId
     *
     * @return mixed
     */
    public function hasChangeOrders($contractId)
    {
        return $this->model
            ->whereId($contractId)
            ->whereHas('changeOrders')
            ->exists();
    }

    /**
     * Is the latest change order on this contract accepted fully?
     *
     * @param int $contractId
     *
     * @return mixed
     */
    public function hasLatestChangeOrderSetAccepted($contractId)
    {
        return $this->find($contractId)
            ->changeOrders()
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->first()
            ->children()
            ->whereNotNull('accepted_at')
            ->exists();
    }

    /**
     * Validate client hash
     *
     * @param int $contractId
     * @param string $clientHash
     *
     * @return mixed
     */
    public function validateClientHash($contractId, $clientHash)
    {
        return $this->model
            ->whereId($contractId)
            ->whereIsClientOwned(true)
            ->whereClientHash($clientHash)
            ->exists();
    }

    /**
     * Create a new Contract, using data from a Proposal
     *
     * @param Proposal $proposal
     * @param int $eventDateRangeId
     *
     * @return Contract
     */
    public function initializeWithProposal(Proposal $proposal, $eventDateRangeId)
    {
        $dateRangeData = $proposal->dateRanges();
        $thisDateRange = $dateRangeData->where('event_date_range_id', $eventDateRangeId)->first();

        $meetingSpaceData = json_decode($thisDateRange->meeting_spaces);
        foreach ($meetingSpaceData as &$item) {
            $spaceRequest = $thisDateRange->eventDateRange->spaceRequests()->whereId($item->id)->first();
            foreach (self::$transferFromSpaceRequest as $field) {
                $item->$field = $spaceRequest->$field;
            }
        }

        $foodBeverageData = json_decode($thisDateRange->food_and_beverage_spaces);
        foreach ($foodBeverageData as &$item) {
            $spaceRequest = $thisDateRange->eventDateRange->spaceRequests()->whereId($item->id)->first();
            foreach (self::$transferFromSpaceRequest as $field) {
                $item->$field = $spaceRequest->$field;
            }
        }

        $transferData = collect($proposal)->only([
            'is_meeting_space_required',
            'is_food_and_beverage_required',
            'attrition_rate',
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
            'notes',
            'questions',
        ])->merge([
            'proposal_id'   => $proposal->id,
            'event_date_range_id'   => $eventDateRangeId,
            'meeting_spaces'    => json_encode($meetingSpaceData),
            'food_and_beverage'    => json_encode($foodBeverageData),
            'is_meeting_space_required' => $proposal->proposalRequest->is_meeting_space_required,
            'is_food_and_beverage_required' => $proposal->proposalRequest->is_food_and_beverage_required,
        ])->toArray();

        $contract = $this->model->create($transferData);

        $contract->reservationMethods()->sync(
            $contract->proposal->proposalRequest->reservationMethods()->pluck('reservation_methods.id')
        );
        $contract->paymentMethods()->sync(
            $contract->proposal->proposalRequest->paymentMethods()->pluck('payment_methods.id')
        );

        return $contract;
    }

    /**
     * Is this contract client-owned?
     *
     * @param int $contractId
     *
     * @return mixed
     */
    public function isClientOwned($contractId)
    {
        return $this->model
            ->whereId($contractId)
            ->whereIsClientOwned(true)
            ->whereNotNull('client_hash')
            ->exists();
    }

    /**
     * Is this contract client-owned?
     *
     * @param int $contractId
     *
     * @return mixed
     */
    public function isOffline($contractId)
    {
        return $this->model
            ->whereId($contractId)
            ->whereIsOfflineContract(true)
            ->exists();
    }

    /**
     * Remove this reservation method from the contract
     *
     * @param int $contractId
     * @param int $methodId
     *
     * @return mixed
     */
    public function removeReservationMethod($contractId, $methodId)
    {
        $this->model
            ->findOrFail($contractId)
            ->reservationMethods()
            ->detach($methodId);
    }

    /**
     * Remove this payment method from the contract
     *
     * @param int $contractId
     * @param int $methodId
     *
     * @return mixed
     */
    public function removePaymentMethod($contractId, $methodId)
    {
        $this->model
            ->findOrFail($contractId)
            ->paymentMethods()
            ->detach($methodId);
    }

    /**
     * Add this reservation method to the contract
     *
     * @param int $contractId
     * @param int $methodId
     *
     * @return mixed
     */
    public function addReservationMethod($contractId, $methodId)
    {
        $this->model
            ->findOrFail($contractId)
            ->reservationMethods()
            ->attach($methodId);
    }

    /**
     * Add this payment method to the contract
     *
     * @param int $contractId
     * @param int $methodId
     *
     * @return mixed
     */
    public function addPaymentMethod($contractId, $methodId)
    {
        $this->model
            ->findOrFail($contractId)
            ->paymentMethods()
            ->attach($methodId);
    }

    /**
     * Make this Contract offline
     *
     * @param int $contractId
     *
     * @return mixed
     */
    public function makeOffline($contractId)
    {
        $this->update(
            $contractId,
            [
                'is_offline_contract'   => true
            ]
        );
    }

    /**
     * Reset a contract's declined status for a licensee
     *
     * @param int $contractId
     *
     * @return mixed
     */
    public function resetDeclineForOwner($contractId)
    {
        return $this->update(
            $contractId,
            [
                'declined_by_owner_at'   => null,
                'declined_by_owner_user' => null,
                'declined_by_owner_because'  => null,
            ]
        );
    }

    /**
     * Mark a contract "declined" for the licensee
     *
     * @param int $contractId
     * @param int $userId
     * @param null|string $reason
     *
     * @return mixed
     */
    public function declineForOwner($contractId, $userId, $reason = null)
    {
        return $this->update(
            $contractId,
            [
                'declined_by_owner_at'   => Carbon::now(),
                'declined_by_owner_user' => $userId,
                'declined_by_owner_because'  => $reason,
            ]
        );
    }

    /**
     * Mark a contract "declined" for the hotel
     *
     * @param int $contractId
     * @param int $userId
     * @param null|string $reason
     *
     * @return mixed
     */
    public function declineForHotel($contractId, $userId, $reason = null)
    {
        return $this->update(
            $contractId,
            [
                'declined_by_hotel_at'   => Carbon::now(),
                'declined_by_hotel_user' => $userId,
                'declined_by_hotel_because'  => $reason,
            ]
        );
    }

    /**
     * Accept contract on behalf of hotel
     *
     * @param int $contractId
     * @param int $userId
     * @param string $signature
     *
     * @return mixed
     */
    public function acceptForHotel($contractId, $userId, $signature)
    {
        return $this->update(
            $contractId,
            [
                'accepted_by_hotel_at'   => Carbon::now(),
                'accepted_by_hotel_user' => $userId,
                'accepted_by_hotel_signature' => $signature,
            ]
        );
    }

    /**
     * Accept contract on behalf of licensee
     *
     * @param int $contractId
     * @param int $userId
     * @param string $signature
     *
     * @return mixed
     */
    public function acceptForOwner($contractId, $userId, $signature)
    {
        return $this->update(
            $contractId,
            [
                'accepted_by_owner_at'   => Carbon::now(),
                'accepted_by_owner_user' => $userId,
                'accepted_by_owner_signature' => $signature,
            ]
        );
    }

    /**
     * Revoke client ownership and remove hash
     *
     * @param int $contractId
     *
     * @return mixed
     */
    public function revokeClientOwnership($contractId)
    {
        $this->update(
            $contractId,
            [
                'is_client_owned'   => false,
                'client_hash'   => null
            ]
        );
    }

    /**
     * Transfer ownership to Client and assign client hash
     *
     * @param int $contractId
     *
     * @return mixed
     */
    public function transferOwnershipToClient($contractId)
    {
        $this->update(
            $contractId,
            [
                'is_client_owned'   => true,
                'client_hash'   => str_random(),
            ]
        );
    }

    /**
     * Can this hotel user access the contract?
     *
     * @param int $userId
     * @param int $contractId
     *
     * @return mixed
     */
    public function userBelongsToHotelOnContract($userId, $contractId)
    {
        return $this->model
            ->whereHas('proposal', function($query) use ($userId) {
                $query->whereHas('hotel', function($query) use ($userId) {
                    $query->whereHas('hoteliers', function($query) use ($userId) {
                        $query->where('users.id', $userId);
                    })->orWhereHas('brand', function($query) use ($userId) {
                        $query->whereExists(function($query) use ($userId) {
                            $query->select(\DB::raw(1))
                                ->from('role_user')
                                ->where(\DB::raw('role_user.user_id'), $userId)
                                ->where(\DB::raw('role_user.rolable_type'), Brand::class)
                                ->whereRaw('role_user.rolable_id = brands.id');
                        });
                    });
                });
            })->exists();
    }

    /**
     * Does this contract belong to this proposal request?
     *
     * @param int $contractId
     * @param int $requestId
     *
     * @return mixed
     */
    public function belongsToProposalRequest($contractId, $requestId)
    {
        return $this->model
            ->whereId($contractId)
            ->whereHas('proposal', function($query) use ($requestId) {
                $query->whereProposalRequestId($requestId);
            })->exists();
    }

    /**
     * Does this contract belong to this licensee?
     *
     * @param int $contractId
     * @param int $licenseeId
     *
     * @return mixed
     */
    public function belongsToLicensee($contractId, $licenseeId)
    {
        return $this->model
            ->whereId($contractId)
            ->whereHas('proposal', function($query) use ($licenseeId) {
                $query->whereHas('proposalRequest', function($query) use ($licenseeId) {
                    $query->whereHas('event', function($query) use ($licenseeId) {
                        $query->whereLicenseeId($licenseeId);
                    });
                });
            })->exists();
    }

    /**
     * Does this contract belong to this hotel?
     *
     * @param int $contractId
     * @param int $hotelId
     *
     * @return mixed
     */
    public function belongsToHotel($contractId, $hotelId)
    {
        return $this->model
            ->whereId($contractId)
            ->whereHas('proposal', function($query) use ($hotelId) {
                $query->whereHotelId($hotelId);
            })->exists();
    }
}
