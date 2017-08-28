<?php

namespace App\Repositories\Contracts;

interface RequestHotelRepositoryInterface
{
    /**
     * Get all users for this hotel and request
     *
     * @param int $hotelId
     * @param int $requestId
     *
     * @return mixed
     */
    public function allUsersForHotelAndRequest($hotelId, $requestId);

    /**
     * Get all RequestHotel objects where (1) the RFP is expiring in X days and
     * (2) the hotel has taken no action
     *
     * @param $daysBeforeExpiration
     *
     * @return mixed
     */
    public function allWithExpiringRequestAndNoActionTaken($daysBeforeExpiration);

    /**
     * Get the RequestHotel for this PR and Hotel
     *
     * @param int $requestId
     * @param int $hotelId
     *
     * @return mixed
     */
    public function findForProposalRequestAndHotel($requestId, $hotelId);

    /**
     * Pull the HotelRequest object that has this user attached for this ProposalRequest
     *
     * @param int $requestId
     * @param int $userId
     * @param string $hash
     *
     * @return mixed
     */
    public function findForRequestAndUserHash($requestId, $userId, $hash);

    /**
     * Get just the hash string for this user on this request hotel
     *
     * @param \App\Models\RequestHotel $requestHotel
     * @param int $userId
     *
     * @return mixed
     */
    public function findHashForRequestHotelAndUser($requestHotel, $userId);

    /**
     *  Can this user access this proposal request?
     *
     * @param int $userId
     * @param int $requestId
     *
     * @return bool
     */
    public function userCanAccessProposalRequest($userId, $requestId);

    /**
     *  Can this user access this proposal request?
     *
     * @param int $userId
     * @param int $proposalId
     *
     * @return bool
     */
    public function userCanAccessProposal($userId, $proposalId);

    /**
     * Is this user attached?
     *
     * @param int $requestId
     * @param int $hotelId
     * @param int $userId
     *
     * @return bool
     */
    public function userIsAttached($requestId, $hotelId, $userId);

    /**
     * Add this user as a contact for this request and hotel
     *
     * @param int $requestId
     * @param int $hotelId
     * @param int $userId
     *
     * @return mixed
     */
    public function attachUser($requestId, $hotelId, $userId);

    /**
     * Remove this user as a contact for this request and hotel
     *
     * @param int $requestId
     * @param int $hotelId
     * @param int $userId
     *
     * @return mixed
     */
    public function detachUser($requestId, $hotelId, $userId);

    /**
     * Mark that the user is queued for contact on behalf of a proposal request
     *
     * @param int $userId
     * @param int $requestId
     * @param int $hotelId
     *
     * @return mixed
     */
    public function initiateContactForProposalRequest($userId, $requestId, $hotelId);
}