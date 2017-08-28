<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface
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
    public function findUsingProposalRequestAndUserHash($userId, $requestId, $hash);

    /**
     * Find a user by ID and the hash stored in the request_recipients pivot table
     *
     * @param int $userId
     * @param int $proposalId
     * @param string $hash
     *
     * @return mixed
     */
    public function findUsingProposalAndUserHash($userId, $proposalId, $hash);

    /**
     * Get all users attached as recipients to this proposal request
     *
     * @param int $requestId
     *
     * @return mixed
     */
    public function allRecipientsForProposalRequest($requestId);

    /**
     * Get all users who are on the managing end of a Proposal Request
     *
     * @param int $requestId
     * @param string|array|null $roleSlug
     *
     * @return mixed
     */
    public function allManagingUsersForProposalRequest($requestId, $roleSlug = null);

    /**
     * Get a colection of all users attached to Client account
     *
     * @param $clientId
     *
     * @return mixed
     */
    public function allForClient($clientId);

    /**
     * Get all users with hotelier role and association with hotel. If hotel
     * is NULL, then all hoteliers for all hotels
     *
     * @param int|null $hotelId
     *
     * @return mixed
     */
    public function allHoteliersForHotel($hotelId = null);

    /**
     * Get a collection of all users attached to the Licensee account
     *
     * @param int $licenseeId
     *
     * @return mixed
     */
    public function allForLicensee($licenseeId);

    /**
     * Get all users attached as a GSO for a brand that are linked as contacts
     * to the supplied licensee
     *
     * @param int $licenseeId
     * @param int|null $brandId
     *
     * @return mixed
     */
    public function allForBrandAndLicensee($licenseeId, $brandId = null);

    /**
     * Get a collection of all users attached to a planner
     *
     * @param $plannerId
     *
     * @return mixed
     */
    public function allForPlanner($plannerId);

    /**
     * Return list of users with ID in inserted set
     *
     * @param array $ids
     *
     * @return mixed
     */
    public function allWithIds($ids = []);

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
    public function hasRole($userId, $roleSlug, $rolableType = null, $rolableId = null);

    /**
     * @param int $userId
     * @param string $roleSlug
     * @param null|string $rolableType
     * @param null|string $rolableId
     *
     * @return mixed
     */
    public function detachRole($userId, $roleSlug, $rolableType = null, $rolableId = null);

    /**
     * Set a User's password
     *
     * @param int $userId
     * @param string $password
     * @param bool $isTemp
     *
     * @return mixed
     */
    public function setPassword($userId, $password, $isTemp = false);

    /**
     * Create a new user account
     *
     * @param string $email
     * @param string $password
     * @param array $attributes
     *
     * @return mixed
     */
    public function createAccount($email, $password, $attributes = []);

    /**
     * Does this user have a hotel user?
     *
     * @param int $userId
     *
     * @return mixed
     */
    public function isHotelUser($userId);
}
