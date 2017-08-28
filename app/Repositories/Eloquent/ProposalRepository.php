<?php

namespace App\Repositories\Eloquent;

use App\Models\Brand;
use App\Repositories\Contracts\ProposalRepositoryInterface;
use App\Repositories\Contracts\ProposalRequestInterface;
use Carbon\Carbon;

class ProposalRepository extends Repository implements ProposalRepositoryInterface
{
    /**
     * Get all Proposals for this Proposal Request
     *
     * @param int $requestId
     *
     * @return mixed
     */
    public function allForProposalRequest($requestId)
    {
        return $this->model
            ->whereProposalRequestId($requestId)
            ->get();
    }

    /**
     * Get all proposals expiring in ## days that have been submitted, but neither
     * accepted nor declined by the licensee
     *
     * @param int $daysLeft
     *
     * @return mixed
     */
    public function allWithUpcomingExpirationAndNoActionTaken($daysLeft)
    {
        return $this->model
            ->where(\DB::raw('DATE(honor_bid_until)'), Carbon::now()->addDays($daysLeft)->format('Y-m-d'))
            ->whereDoesntHave('contracts')
            ->whereHas('dateRanges', function($query) {
                $query->whereNull('declined_at')
                    ->whereNotNull('submitted_at');
            })
            ->get();
    }

    /**
     * Get all proposals associated with a hotel user's hotels
     *
     * @param int $userId
     * @param string|null $startDate
     * @param string|null $endDate
     *
     * @return mixed
     */
    public function allForHotelierUser($userId, $startDate = null, $endDate = null)
    {
        $query = $this->model
            ->whereHas('proposalRequest', function($query) use ($userId) {
                $query->whereHas('requestHotels', function($query) use ($userId) {
                    $query->whereHas('hotel', function($query) use ($userId) {
                        $query->whereHas('hoteliers', function($query) use ($userId) {
                            $query->where('users.id', $userId);
                        });
                    });
                });
            });

        return $this->addDateQuery($query, $startDate, $endDate)->get();
    }

    /**
     * Get all proposals associated with a GSO user's brands
     *
     * @param int $userId
     * @param string|null $startDate
     * @param string|null $endDate
     *
     * @return mixed
     */
    public function allForGSOUser($userId, $startDate = null, $endDate = null)
    {
        $query = $this->model
            ->whereHas('proposalRequest', function($query) use ($userId) {
                $query->whereHas('requestHotels', function($query) use ($userId) {
                    $query->whereHas('hotel', function($query) use ($userId) {
                        $query->whereHas('brand', function($query) use ($userId) {
                            $query->whereExists(function($query) use ($userId) {
                                $query->select(\DB::raw(1))
                                    ->from('role_user')
                                    ->where(\DB::raw('role_user.user_id'), $userId)
                                    ->where(\DB::raw('role_user.rolable_type'), Brand::class)
                                    ->whereRaw('role_user.rolable_id = brands.id');
                            });
                        });
                    });
                });
            });

        return $this->addDateQuery($query, $startDate, $endDate)->get();
    }

    /**
     * Does this proposal have a contract
     *
     * @param int $proposalId
     *
     * @return mixed
     */
    public function hasContract($proposalId)
    {
        return $this->model
            ->whereId($proposalId)
            ->whereHas('contracts')
            ->exists();
    }

    /**
     * Verify that this user has access to this proposal, using hash
     * attached to join table row
     *
     * @param int $proposalId
     * @param int $userId
     *
     * @return mixed
     */
    public function checkUserAccess($proposalId, $userId)
    {
        $proposal = $this->find($proposalId);

        return $proposal
            ->whereHas('usersReceived', function ($query) use ($userId) {
                $query->where('users.id', $userId);
            })
            ->exists();
    }

    /**
     * Get all proposals for a particular hotel with an optional user attached
     *
     * @param int $hotelId
     * @param int|null $userId
     *
     * @return mixed
     */
    public function getForHotelAndUser($hotelId, $userId = null)
    {
        $baseQuery = $this->model
            ->whereHotelId($hotelId);

        return $baseQuery
            ->with('proposalRequest.event.dateRanges', 'dateRanges')
            ->get();
    }

    /**
     * Get the proposal for this request and hotel
     *
     * @param int $requestId
     * @param int $hotelId
     *
     * @return mixed
     */
    public function findForProposalRequestAndHotel($requestId, $hotelId)
    {
        return $this->model
            ->whereProposalRequestId($requestId)
            ->whereHotelId($hotelId)
            ->first();
    }

    /**
     * Create a new proposal for this hotel
     *
     * @param int $hotelId
     *
     * @return mixed
     */
    public function storeForRequestAndHotel($proposalRequestId, $hotelId)
    {
        return $this->store([
            'hotel_id'  => $hotelId,
            'proposal_request_id' => $proposalRequestId,
        ]);
    }

    /**
     * Add a user to the proposal
     *
     * @param int $proposalId
     * @param int $userId
     * @param array $contactInfo
     *
     * @return mixed
     */
    public function addUser($proposalId, $userId, $contactInfo = [])
    {
        $pivotData = collect($contactInfo)
            ->merge(['hash' => str_random()])
            ->toArray();

        return $this->find($proposalId)->usersReceived()->attach($userId, $pivotData);
    }

    /**
     * Remove user from proposal
     *
     * @param int $proposalId
     * @param int $userId
     *
     * @return mixed
     */
    public function removeUser($proposalId, $userId)
    {
        return $this->find($proposalId)->usersReceived()->detach($userId);
    }

    /**
     * Mark a proposal "submitted"
     *
     * @param int $proposalId
     * @param int $eventDateRangeId
     * @param int $userId
     *
     * @return mixed
     */
    public function submit($proposalId, $eventDateRangeId, $userId)
    {
        $this->find($proposalId)
            ->dateRanges()
            ->whereEventDateRangeId($eventDateRangeId)
            ->first()
            ->update([
                'submitted_by_user'  => $userId,
                'submitted_at'  => Carbon::now(),
            ]);
    }

    /**
     * Decline a proposal
     *
     * @param int $proposalId
     * @param int $eventDateRangeId
     * @param int $userId
     * @param string $userType
     * @param string|null $reason
     *
     * @return mixed
     */
    public function decline($proposalId, $eventDateRangeId, $userId, $userType, $reason = null)
    {
        $this->find($proposalId)
            ->dateRanges()
            ->whereEventDateRangeId($eventDateRangeId)
            ->first()
            ->update([
                'declined_by_user'  => $userId,
                'declined_by_user_type' => $userType,
                'declined_because' => $reason,
                'declined_at'  => Carbon::now(),
            ]);
    }

    /**
     * Reset the decline for a proposal
     *
     * @param int $proposalId
     * @param int $eventDateRangeId
     * @param string $userType
     *
     * @return mixed
     */
    public function resetDecline($proposalId, $eventDateRangeId, $userType)
    {
        $this->find($proposalId)
            ->dateRanges()
            ->whereEventDateRangeId($eventDateRangeId)
            ->whereDeclinedByUserType($userType)
            ->first()
            ->update([
                'declined_by_user'  => null,
                'declined_by_user_type' => null,
                'declined_because' => null,
                'declined_at'  => null,
            ]);
    }

    /**
     * Add a date query to the query
     *
     * @param $query
     * @param null|string $startDate
     * @param null|string $endDate
     */
    private function addDateQuery($query, $startDate = null, $endDate = null)
    {
        return $query->whereHas('proposalRequest', function($query) use ($startDate, $endDate) {
            $query->whereHas('event', function ($query) use ($startDate, $endDate) {
                if ($startDate || $endDate) {
                    $query->whereHas('dateRanges', function($query) use ($startDate, $endDate)
                    {
                        if ($startDate && $endDate) {
                            $query->where('start_date', '>=', $startDate)
                                ->orWhere('end_date', '>=', $endDate);
                        } else {
                            if ($startDate) {
                                $query->where('start_date', '>=', $startDate);
                            }
                            if ($endDate) {
                                $query->where('end_date', '>=', $endDate);
                            }
                        }
                    });
                }
            });
        });
    }

    /**
     * Create a date range for this proposal
     *
     * @param int $proposalId
     * @param int $eventDateRangeId
     *
     * @return mixed
     */
    public function initializeDateRange($proposalId, $eventDateRangeId)
    {
        $this->find($proposalId)
            ->dateRanges()
            ->create([
                'event_date_range_id'   => $eventDateRangeId,
                'meeting_spaces'    => json_encode([]),
                'food_and_beverage_spaces'    => json_encode([]),
                'rooms'    => json_encode([]),
            ]);
    }

    /**
     * Update date range data for proposal
     *
     * @param int $proposalId
     * @param int $eventDateRangeId
     * @param array $data
     *
     * @return mixed
     */
    public function updateDateRange($proposalId, $eventDateRangeId, $data = [])
    {
        $this->find($proposalId)
            ->dateRanges()
            ->whereEventDateRangeId($eventDateRangeId)
            ->update([
                'rooms' => json_encode($data['rooms']),
                'meeting_spaces' => json_encode($data['meeting_spaces']),
                'food_and_beverage_spaces' => json_encode($data['food_and_beverage_spaces']),
            ]);
    }

    /**
     * Can this hotel user access the proposal?
     *
     * @param int $userId
     * @param int $proposalId
     *
     * @return mixed
     */
    public function userBelongsToHotelOnProposal($userId, $proposalId)
    {
        return $this->model
            ->whereHas('hotel', function($query) use ($userId) {
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
            })->exists();
    }

    /**
     * Update the honor_bid_until values for all of this licensee's proposals by offset value
     *
     * @param int $licenseeId
     * @param int $offset
     *
     * @return mixed
     */
    public function adjustTimezones($licenseeId, $offset)
    {
        if ($offset == 0) {
            return true;
        }

        $this->model
            ->whereHas('proposalRequest', function($query) use ($licenseeId) {
                $query->whereHas('event', function($query) use ($licenseeId) {
                    $query->whereLicenseeId($licenseeId);
                });
            })
            ->update([
                'honor_bid_until' =>
                    \DB::raw(
                        sprintf(
                            (app()->environment() == 'testing')
                                ? 'DATETIME(honor_bid_until, "%d hour")'
                                : 'DATE_ADD(honor_bid_until, INTERVAL %d HOUR)',
                            $offset
                        )
                    )
            ]);
    }

    /**
     * Does this proposal belong to this licensee?
     *
     * @param int $proposalId
     * @param int $licenseeId
     *
     * @return mixed
     */
    public function belongsToLicensee($proposalId, $licenseeId)
    {
        return $this->model
            ->whereId($proposalId)
            ->whereHas('proposalRequest', function($query) use ($licenseeId) {
                $query->whereHas('event', function($query) use ($licenseeId) {
                    $query->whereLicenseeId($licenseeId);
                });
            })->exists();
    }
}
