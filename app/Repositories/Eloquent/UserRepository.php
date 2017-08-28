<?php

namespace App\Repositories\Eloquent;

use App\Models\Brand;
use App\Models\Client;
use App\Models\Licensee;
use App\Models\Planner;
use App\Repositories\Contracts\UserRepositoryInterface;
use Carbon\Carbon;

class UserRepository extends Repository implements UserRepositoryInterface
{
    /**
     * Find a user by ID and the hash stored in the request_recipients pivot table
     *
     * @param int $userId
     * @param int $requestId
     * @param string $hash
     *
     * @return mixed
     */
    public function findUsingProposalRequestAndUserHash($userId, $requestId, $hash)
    {
        return $this->model
            ->whereId($userId)
            ->whereHas('requestHotels', function($query) use ($requestId, $hash)
            {
                $query->whereProposalRequestId($requestId)
                    ->where('request_hotel_user.hash', $hash);
            })->first();
    }

    /**
     * Find a user by ID and the hash stored in the request_recipients pivot table
     *
     * @param int $userId
     * @param int $proposalId
     * @param string $hash
     *
     * @return mixed
     */
    public function findUsingProposalAndUserHash($userId, $proposalId, $hash)
    {
        return $this->model
            ->whereId($userId)
            ->whereHas('requestHotels', function($query) use ($proposalId, $hash)
            {
                $query->where('request_hotel_user.hash', $hash)
                    ->whereHas('proposalRequest', function($query) use ($proposalId)
                    {
                        $query->whereHas('proposals', function($query) use ($proposalId)
                        {
                            $query->whereId($proposalId);
                        });
                    });
            })->first();
    }

    /**
     * Get all users attached as recipients to this proposal request
     *
     * @param int $requestId
     *
     * @return mixed
     */
    public function allRecipientsForProposalRequest($requestId)
    {
        return $this->model
            ->whereHas('requestHotels', function($query) use ($requestId)
            {
                $query->whereProposalRequestId($requestId);
            })
            ->with('requestHotels')
            ->get();
    }

    /**
     * Get all users who are on the managing end of a Proposal Request
     *
     * @param int $requestId
     * @param string|null $roleSlug
     *
     * @return mixed
     */
    public function allManagingUsersForProposalRequest($requestId, $roleSlug = null)
    {
        $baseQuery = $this->model
            ->whereHas('proposalRequestsManaging', function($query) use ($requestId) {
                $query->where('proposal_requests.id', $requestId);
            });

        if ($roleSlug) {

            $baseQuery = $baseQuery->whereHas('roles', function($query) use ($roleSlug) {
                (is_array($roleSlug)) ? $query->whereIn('slug', $roleSlug) : $query->whereSlug($roleSlug);
            });
        }

        return $baseQuery->get();
    }

    /**
     * Get all users with hotelier role and association with hotel
     *
     * @param int|null $hotelId
     *
     * @return mixed
     */
    public function allHoteliersForHotel($hotelId = null)
    {
        return $this->model
            ->whereHas('hotels', function($query) use ($hotelId)
            {
                if ($hotelId != null) {
                    $query->where('hotels.id', $hotelId);
                }
            })
            ->whereHas('roles', function($query)
            {
                $query->whereSlug('hotelier');
            })
            ->with('hotels')
            ->get();
    }

    /**
     * Get a collection of all users attached to the Licensee account
     *
     * @param int $licenseeId
     *
     * @return mixed
     */
    public function allForLicensee($licenseeId)
    {
        return $this->model
            ->with('roles')
            ->whereHas('roles', function ($query) use ($licenseeId) {
                $query->where('role_user.rolable_type', Licensee::class)
                    ->where('role_user.rolable_id', $licenseeId);
            })->get();
    }

    /**
     * Get all users attached as a GSO for a brand that are linked as contacts
     * to the supplied licensee
     *
     * @param int|null $brandId
     * @param int $licenseeId
     *
     * @return mixed
     */
    public function allForBrandAndLicensee($licenseeId, $brandId = null)
    {
        return $this->model
            ->whereHas('roles', function($query) use ($brandId) {
                $query->whereSlug('hotelso');
                if ($brandId != null) {
                    $query->where('role_user.rolable_type', Brand::class)
                        ->where('role_user.rolable_id', $brandId);
                }
            })
            ->whereHas('licenseesWithContact', function($query) use ($licenseeId)
            {
                $query->where('licensees.id', $licenseeId);
            })
            ->with('roles')
            ->get();
    }

    /**
     * Get a collection of all users attached to Client account
     *
     * @param $clientId
     *
     * @return mixed
     */
    public function allForClient($clientId)
    {
        return $this->model
            ->with('roles')
            ->whereHas('roles', function ($query) use ($clientId) {
                $query->where('role_user.rolable_type', Client::class)
                    ->where('role_user.rolable_id', $clientId);
            })->get();
    }

    /**
     * Get a collection of all users attached to a planner
     *
     * @param $plannerId
     *
     * @return mixed
     */
    public function allForPlanner($plannerId)
    {
        return $this->model
            ->with('roles')
            ->whereHas('roles', function ($query) use ($plannerId) {
                $query->where('role_user.rolable_type', Planner::class)
                    ->where('role_user.rolable_id', $plannerId);
            })->get();
    }

    /**
     * Return list of users with ID in inserted set
     *
     * @param array $ids
     *
     * @return mixed
     */
    public function allWithIds($ids = [])
    {
        return $this->model
            ->whereIn('id', $ids)
            ->get();
    }

    /**
     * Determine if this user has the supplied role, with optional pivot params
     *
     * @param int $userId
     * @param string $roleSlug
     * @param null|string $rolableType
     * @param null|string $rolableId
     *
     * @return mixed
     */
    public function hasRole($userId, $roleSlug, $rolableType = null, $rolableId = null)
    {
        return $this->model
            ->where('users.id', $userId)
            ->whereHas('roles', function($query) use ($roleSlug, $rolableType, $rolableId) {
                $query->whereSlug($roleSlug);
                if ($rolableId && $rolableType) {
                    $query->where('role_user.rolable_id', $rolableId)
                        ->where('role_user.rolable_type', $rolableType);
                }
            })
            ->exists();
    }

    /**
     * @param int $userId
     * @param string $roleSlug
     * @param null|string $rolableType
     * @param null|string $rolableId
     *
     * @return mixed
     */
    public function detachRole($userId, $roleSlug, $rolableType = null, $rolableId = null)
    {
        $user = $this->find($userId);
        $role = $user->roles()
            ->whereSlug($roleSlug)
            ->where('role_user.rolable_type', $rolableType)
            ->where('role_user.rolable_id', $rolableId)
            ->first();

        $user->roles()->detach($role->id);

        return $user;
    }

    /**
     * Create a new user account
     *
     * @param string $email
     * @param string $password
     * @param array $attributes
     *
     * @return mixed
     */
    public function createAccount($email, $password, $attributes = [])
    {
        if ($password) {
            $password = \Hash::make($password);
        }

        return $this->model->create(
            collect($attributes)
                ->merge(compact('email', 'password'))
                ->merge([
                    'hash'  => str_random(),
                ])
                ->toArray()
        );
    }

    /**
     * Set a User's password
     *
     * @param int $userId
     * @param string $password
     * @param bool $isTemp
     *
     * @return mixed
     */
    public function setPassword($userId, $password, $isTemp = false)
    {
        return $this->update(
            $userId,
            [
                'password'      => \Hash::make($password),
                'is_temp_password'  => $isTemp,
            ]
        );
    }

    /*
     * Does this user have a hotel role?
     *
     * @param int $userId
     *
     * @return mixed
     */
    public function isHotelUser($userId)
    {
        return ($this->hasRole($userId, 'hotelso') || $this->hasRole($userId, 'hotelier'));
    }
}
