<?php

namespace App\Repositories\Eloquent;

use App\Models\Brand;
use App\Repositories\Contracts\RequestHotelRepositoryInterface;
use Carbon\Carbon;

class RequestHotelRepository extends Repository implements RequestHotelRepositoryInterface
{
    /**
     * Get the RequestHotel for this PR and Hotel
     *
     * @param int $requestId
     * @param int $hotelId
     *
     * @return mixed
     */
    public function findForProposalRequestAndHotel($requestId, $hotelId)
    {
        return $this->model
            ->whereHotelId($hotelId)
            ->whereProposalRequestId($requestId)
            ->first();
    }

    /**
     * Get all RequestHotel objects where (1) the RFP is expiring in X days and
     * (2) the hotel has taken no action
     *
     * @param int $daysBeforeExpiration
     *
     * @return mixed
     */
    public function allWithExpiringRequestAndNoActionTaken($daysBeforeExpiration)
    {
        return $this->model
            ->whereHas('proposalRequest', function($query) use ($daysBeforeExpiration) {
                $query->where(\DB::raw('DATE(cutoff_date)'), Carbon::now()->addDays($daysBeforeExpiration)->format('Y-m-d'));
            })
            ->whereHas('hotel', function($query) {
                $query->whereHas('proposals', function($query) {
                    $query->whereRaw('proposals.proposal_request_id = hotel_proposal_request.proposal_request_id')
                        ->whereDoesntHave('dateRanges', function($query) {
                            $query->whereNotNull('declined_at')
                                ->orWhereNotNull('submitted_at');
                        });
                });
            })
            ->get()
            ->merge(
                $this->model
                    ->whereHas('proposalRequest', function($query) use ($daysBeforeExpiration) {
                        $query->where(\DB::raw('DATE(cutoff_date)'), Carbon::now()->addDays($daysBeforeExpiration)->format('Y-m-d'));
                    })
                    ->whereHas('hotel', function($query) {
                        $query->whereDoesntHave('proposals', function($query) {
                            $query->whereRaw('proposals.proposal_request_id = hotel_proposal_request.proposal_request_id');
                        });
                    })->get()
            );
    }

    /**
     * Get just the hash string for this user on this request hotel
     *
     * @param \App\Models\RequestHotel $requestHotel
     * @param int $userId
     *
     * @return mixed
     */
    public function findHashForRequestHotelAndUser($requestHotel, $userId)
    {
        return $requestHotel->users()
            ->where('users.id', $userId)
            ->first()
            ->pivot
            ->hash;
    }

    /**
     * Is this user attached?
     *
     * @param int $requestId
     * @param int $hotelId
     * @param int $userId
     *
     * @return bool
     */
    public function userIsAttached($requestId, $hotelId, $userId)
    {
        return $this->model
            ->whereProposalRequestId($requestId)
            ->whereHotelId($hotelId)
            ->whereHas('users', function($query) use ($userId) {
                $query->where('users.id', $userId);
            })->exists();
    }

    /**
     * At this user as a contact for this request and hotel
     *
     * @param int $requestId
     * @param int $hotelId
     * @param int $userId
     *
     * @return mixed
     */
    public function attachUser($requestId, $hotelId, $userId)
    {
        $object = $this->model
            ->whereProposalRequestId($requestId)
            ->whereHotelId($hotelId)
            ->first();

        if ($object->users()->where('users.id', $userId)->count() > 0) {
            return false;
        }

        $object
            ->users()
            ->attach(
                $userId,
                [
                    'hash'  => str_random(),
                ]
            );
    }

    /**
     * Pull the HotelRequest object that has this user attached for this ProposalRequest
     *
     * @param int $requestId
     * @param int $userId
     * @param string $hash
     *
     * @return mixed
     */
    public function findForRequestAndUserHash($requestId, $userId, $hash)
    {
        return $this->model
            ->whereProposalRequestId($requestId)
            ->whereHas('users', function($query) use ($userId, $hash)
            {
                $query->where('users.id', $userId)
                    ->where('request_hotel_user.hash', $hash);
            })->first();
    }

    /**
     *  Can this user access this proposal request?
     *
     * @param int $userId
     * @param int $requestId
     *
     * @return bool
     */
    public function userCanAccessProposalRequest($userId, $requestId)
    {
        return $this->model
            ->whereProposalRequestId($requestId)
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
     *  Can this user access this proposal?
     *
     * @param int $userId
     * @param int $proposalId
     *
     * @return bool
     */
    public function userCanAccessProposal($userId, $proposalId)
    {
        return $this->model
            ->whereHas('proposalRequest', function($query) use ($proposalId)
            {
                $query->whereHas('proposals', function($query) use ($proposalId)
                {
                    $query->whereId($proposalId);
                });
            })
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
     * Remove this user as a contact for this request and hotel
     *
     * @param int $requestId
     * @param int $hotelId
     * @param int $userId
     *
     * @return mixed
     */
    public function detachUser($requestId, $hotelId, $userId)
    {
        $this->model
            ->whereProposalRequestId($requestId)
            ->whereHotelId($hotelId)
            ->first()
            ->users()
            ->detach($userId);
    }

    /**
     * Get all users for this hotel and request
     *
     * @param int $hotelId
     * @param int $requestId
     *
     * @return mixed
     */
    public function allUsersForHotelAndRequest($hotelId, $requestId)
    {
        return $this->model
            ->whereProposalRequestId($requestId)
            ->whereHotelId($hotelId)
            ->first()
            ->users()
            ->get();
    }

    /**
     * Mark that the user is queued for contact on behalf of a proposal request
     *
     * @param int $userId
     * @param int $requestId
     * @param int $hotelId
     *
     * @return mixed
     */
    public function initiateContactForProposalRequest($userId, $requestId, $hotelId)
    {
        $user = $this->model
            ->whereProposalRequestId($requestId)
            ->whereHotelId($hotelId)
            ->first()
            ->users()
            ->updateExistingPivot(
                $userId,
                [
                    'contact_initiated_at'  => Carbon::now()
                ]
            );
    }
}
