<?php

namespace App\Repositories\Eloquent;

use App\Models\Brand;
use App\Models\EventLocation;
use App\Models\ProposalRequest;
use App\Models\RoomRequestDate;
use App\Models\SpaceRequest;
use App\Repositories\Contracts\Collection;
use App\Repositories\Contracts\ProposalRequestRepositoryInterface;
use App\Repositories\Eloquent\Repository;
use Carbon\Carbon;

class ProposalRequestRepository extends Repository implements ProposalRequestRepositoryInterface
{
    /**
     * Create a new ProposalRequest for the given Event
     *
     * @param int $eventId
     * @param array $attributes
     *
     * @return mixed
     */
    public function storeForEvent($eventId, $attributes = [])
    {
        return $this->store(
            collect($attributes)
                ->merge([
                    'event_id'  => $eventId,
                ])
                ->toArray()
        );
    }

    /**
     * Does this ProposalRequest have at least one Proposal?
     *
     * @param int $requestId
     *
     * @return mixed
     */
    public function hasProposals($requestId)
    {
        return $this->model
            ->whereId($requestId)
            ->whereHas('proposals', function () {
            })
            ->exists();
    }

    /**
     * Does this PR have disbursements?
     *
     * @param int $requestId
     *
     * @return mixed
     */
    public function hasDisbursements($requestId)
    {
        return $this->model
            ->whereId($requestId)
            ->whereHas('requestHotels', function($query) {
                $query->whereHas('users', function($query) {
                    $query->whereNotNull('request_hotel_user.contact_initiated_at');
                });
            })->exists();
    }

    /**
     * Add a user to the proposal request, storing temporary contact information
     * in pivot table. Assign the user an access hash
     *
     * @param int $requestId
     * @param int $userId
     * @param array $contactInfo
     *
     * @return mixed
     */
    public function addUser($requestId, $userId, $contactInfo = [])
    {
        $pivotData = collect($contactInfo)
            ->merge(['hash' => str_random()])
            ->toArray();

        return $this->find($requestId)->usersManaging()->attach($userId, $pivotData);
    }

    /**
     * Delete all users from proposal request (use for syncing purposes)
     *
     * @param int $requestId
     *
     * @return mixed
     */
    public function clearUsers($requestId)
    {
        $this->find($requestId)->usersManaging()->detach();
    }

    /**
     * Get all proposal requests for hotel
     *
     * @param int $hotelId
     *
     * @return Collection
     */
    public function allForHotel($hotelId)
    {
        return $this->model
            ->whereHas('proposals', function ($query) use ($hotelId) {
                $query->whereHotelId($hotelId);
            })
            ->with('event.dateRanges')
            ->get();
    }

    /**
     * Get all proposal requests belonging to a licensee
     *
     * @param int $licenseeId
     *
     * @return mixed
     */
    public function allForLicensee($licenseeId)
    {
        return $this->model
            ->whereHas('event', function ($query) use ($licenseeId) {
                $query->whereHas('licensee', function ($query) use ($licenseeId) {
                    $query->whereId($licenseeId);
                });
            })
            ->get();
    }

    /**
     * All PRs for a licensee without any date ranges attached
     *
     * @param int $licenseeId
     *
     * @return mixed
     */
    public function allForLicenseeWithoutDateRanges($licenseeId)
    {
        return $this->model
            ->whereHas('event', function($query) use ($licenseeId) {
                $query->where('licensee_id', $licenseeId)
                    ->whereDoesntHave('dateRanges');
            })->get();
    }

    /**
     * @param $licenseeId
     *
     * @param null|string $startDate
     * @param null|string $endDate
     *
     * @return mixed
     */
    public function allRfpStageForLicenseeWithDateRange($licenseeId, $startDate = null, $endDate = null)
    {
        $query = $this->model
            ->whereHas('event', function ($query) use ($licenseeId) {
                $query->whereHas('licensee', function ($query) use ($licenseeId) {
                    $query->whereId($licenseeId);
                });
            })
            ->whereDoesntHave('proposals', function($query) {
                $query->whereHas('contracts');
            });

        return $this->addDateQuery($query, $startDate, $endDate)->get();
    }

    /**
     * @param $licenseeId
     *
     * @param null|string $startDate
     * @param null|string $endDate
     *
     * @return mixed
     */
    public function allContractStageForLicenseeWithDateRange($licenseeId, $startDate = null, $endDate = null)
    {
        $query = $this->model
            ->whereHas('event', function ($query) use ($licenseeId) {
                $query->whereHas('licensee', function ($query) use ($licenseeId) {
                    $query->whereId($licenseeId);
                });
            })
            ->whereHas('proposals', function($query) {
                $query->whereHas('contracts');
            });

        return $this->addDateQuery($query, $startDate, $endDate)->get();
    }

    /**
     * Get all proposal requests belonging to a licensee
     *
     * @param int $licenseeId
     * @param null $startDate
     * @param null $endDate
     *
     * @return mixed
     */
    public function allForLicenseeWithDateRange($licenseeId, $startDate = null, $endDate = null)
    {
        $query = $this->model
            ->whereHas('event', function ($query) use ($licenseeId, $startDate, $endDate) {
                $query->whereHas('licensee', function ($query) use ($licenseeId) {
                    $query->whereId($licenseeId);
                });
            });
        return $this->addDateQuery($query, $startDate, $endDate)->get();
    }

    /**
     * Get all proposal requests belonging to a hotel
     *
     * @param int $userId
     * @param null $startDate
     * @param null $endDate
     *
     * @return mixed
     */
    public function allForHotelierWithDateRange($userId, $startDate = null, $endDate = null)
    {
        $query = $this->model
            ->whereHas('requestHotels', function($query) use ($userId) {
                $query->whereHas('hotel', function($query) use ($userId) {
                    $query->whereHas('hoteliers', function($query) use ($userId) {
                        $query->where('users.id', $userId);
                    });
                });
            });
        return $this->addDateQuery($query, $startDate, $endDate)->get();
    }

    /**
     * Get all proposal requests belonging to hotels of a GSO's brands
     *
     * @param int $userId
     * @param null $startDate
     * @param null $endDate
     *
     * @return mixed
     */
    public function allForGSOWithDateRange($userId, $startDate = null, $endDate = null)
    {
        $query = $this->model
            ->whereHas('requestHotels', function($query) use ($userId) {
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
        return $this->addDateQuery($query, $startDate, $endDate)->get();
    }

    /**
     * Get all unread Proposal Requests for this User, including the hash associating the user
     * with the PR
     *
     * @param int $userId
     *
     * @return mixed
     */
    public function allUnreadForUser($userId)
    {
        $requests = $this->model
            ->whereHas('requestHotels', function($query) use ($userId) {
                $query->whereHas('users', function($query) use ($userId) {
                    $query->where('users.id', $userId)
                        ->whereNotNull('request_hotel_user.contact_initiated_at');
                });
            })
            ->with(['requestHotels'])
            ->get();

        $forgottenCount = 0;
        foreach ($requests as $i => &$request) {
            $requestHotel = $request->requestHotels()->whereHas('users', function($query) use ($userId) {
                $query->where('users.id', $userId);
            })->first();

            if ($request->proposals()->whereHotelId($requestHotel->hotel_id)->exists()) {
                $requests->forget($i);
                $forgottenCount++;
                continue;
            }

            $request->hash = $requestHotel->users()->where('users.id', $userId)->first()->pivot->hash;
            $request->hotel_id = $requestHotel->hotel_id;
            $request->user_id = $userId;
        }

        if ($forgottenCount > 0) {
            $requests = $requests->flatten(1);
        }

        return $requests;
    }

    /**
     * Duplicate this proposal request and attach to event
     *
     * @param int $requestId
     * @param int $eventId
     *
     * @return mixed
     */
    public function duplicateWithEventId($requestId, $eventId)
    {
        $request = $this->find($requestId);

        $newRequest = $request->replicate([
            'event_id'  => $eventId,
            'cutoff_date',
            'id',
        ]);
        $newRequest->event_id = $eventId;
        $newRequest->save();

        foreach ($request->eventLocations as $location) {
            $newRequest->eventLocations()->attach(
                $location->id
            );
        }

        foreach ($request->questionGroups as $group) {
            $newGroup = $group->replicate([
                'proposal_request_id',
                'id'
            ]);
            $newGroup->proposal_request_id = $newRequest->id;
            $newGroup->save();

            foreach ($group->questions as $question) {
                $newQuestion = $question->replicate([
                    'id',
                    'request_question_group_id',
                ]);
                $newQuestion->request_question_group_id = $newGroup->id;
                $newQuestion->save();
            }
        }

        foreach ($request->event->dateRanges as $dateRange) {

            $newDateRange = $dateRange->replicate();
            $newRequest->event->dateRanges()->save($newDateRange);

            foreach ($dateRange->roomRequestDates as $roomRequestDate) {
                $newRequestDate = $roomRequestDate->replicate([
                    'event_date_range_id',
                    'proposal_request_id',
                ]);
                $newRequestDate->event_date_range_id = $newDateRange->id;
                $newRequestDate->proposal_request_id = $newRequest->id;
                $newDateRange->roomRequestDates()->save($newRequestDate);
            }

            foreach ($dateRange->spaceRequests as $spaceRequest) {
                $newSpaceRequest = $spaceRequest->replicate([
                    'proposal_request_id',
                    'event_date_range_id',
                ]);
                $newSpaceRequest->event_date_range_id = $newDateRange->id;
                $newSpaceRequest->proposal_request_id = $newRequest->id;
                $newDateRange->spaceRequests()->save($newSpaceRequest);
            }
        }

        return $newRequest;
    }

    /**
     * All requests for given hotel with upcoming cutoff dates,
     * and no signed contract
     *
     * @param int $hotelId
     *
     * @return mixed
     */
    public function withUpcomingCutoffDatesForHotel($hotelId)
    {
        return $this->model
            ->where('cutoff_date', '>=', Carbon::today())
            ->whereHas('proposals', function ($query) use ($hotelId) {
                $query->whereHotelId($hotelId)
                    ->whereDoesntHave('contracts', function ($query) {
                        $query->whereNotNull('accepted_by_owner_at')
                            ->whereNotNull('accepted_by_hotel_at');
                    });
            })
            ->with('event.dateRanges', 'contracts')
            ->get();
    }

    /**
     * Sync the passed in location IDs with the given Proposal Request
     *
     * @param int $requestId
     * @param array $locationIds
     *
     * @return mixed
     */
    public function syncEventLocations($requestId, $locationIds = [])
    {
        $request = $this->find($requestId);

        $request->eventLocations()->sync($locationIds);
    }

    /**
     * Sync reservation methods
     *
     * @param int $requestId
     * @param array $methodIds
     *
     * @return mixed
     */
    public function syncReservationMethods($requestId, $methodIds = [])
    {
        $this->model
            ->findOrFail($requestId)
            ->reservationMethods()
            ->sync($methodIds);
    }

    /**
     * Sync payment methods
     *
     * @param int $requestId
     * @param array $methodIds
     *
     * @return mixed
     */
    public function syncPaymentMethods($requestId, $methodIds = [])
    {
        $this->model
            ->findOrFail($requestId)
            ->paymentMethods()
            ->sync($methodIds);
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
        return $query->whereHas('event', function ($query) use ($startDate, $endDate) {
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
    }

    /**
     * Does this Proposal Request belong to this Licensee?
     *
     * @param int $requestId
     * @param int $licenseeId
     *
     * @return bool
     */
    public function belongsToLicensee($requestId, $licenseeId)
    {
        return $this->model
            ->whereId($requestId)
            ->whereHas('event', function($query) use ($licenseeId) {
                $query->whereLicenseeId($licenseeId);
            })->exists();
    }

    /**
     * Adjust timezones on cutoff_date for all PRs for this licensee
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
            ->whereHas('event', function($query) use ($licenseeId) {
                $query->whereLicenseeId($licenseeId);
            })
            ->update([
                'cutoff_date' =>
                    \DB::raw(
                        sprintf(
                            (app()->environment() == 'testing')
                                ? 'DATETIME(cutoff_date, "%d hour")'
                                : 'DATE_ADD(cutoff_date, INTERVAL %d HOUR)',
                            $offset
                        )
                    )
            ]);
    }
}
