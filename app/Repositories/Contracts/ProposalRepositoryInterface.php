<?php

namespace App\Repositories\Contracts;

interface ProposalRepositoryInterface
{
    /**
     * Get all Proposals for this Proposal Request
     *
     * @param int $requestId
     *
     * @return mixed
     */
    public function allForProposalRequest($requestId);

    /**
     * Get all proposals expiring in ## days that have been submitted, but neither
     * accepted nor declined by the licensee
     *
     * @param int $daysLeft
     *
     * @return mixed
     */
    public function allWithUpcomingExpirationAndNoActionTaken($daysLeft);

    /**
     * Get all proposals associated with a hotel user's hotels
     *
     * @param int $userId
     * @param null $startDate
     * @param null $endDate
     *
     * @return mixed
     */
    public function allForHotelierUser($userId, $startDate = null, $endDate = null);

    /**
     * Get all proposals associated with a GSO user's brands
     *
     * @param int $userId
     * @param string|null $startDate
     * @param string|null $endDate
     *
     * @return mixed
     */
    public function allForGSOUser($userId, $startDate = null, $endDate = null);

    /**
     * Does this proposal have a contract
     *
     * @param int $proposalId
     *
     * @return mixed
     */
    public function hasContract($proposalId);

    /**
     * Verify that this user has access to this proposal, using hash
     * attached to join table row
     *
     * @param int $proposalId
     * @param int $userId
     *
     * @return mixed
     */
    public function checkUserAccess($proposalId, $userId);

    /**
     * Get the proposal for this request and hotel
     *
     * @param int $requestId
     * @param int $hotelId
     *
     * @return mixed
     */
    public function findForProposalRequestAndHotel($requestId, $hotelId);

    /**
     * Get all proposals for a particular hotel with an optional user attached
     *
     * @param int $hotelId
     * @param int|null $userId
     *
     * @return mixed
     */
    public function getForHotelAndUser($hotelId, $userId = null);

    /**
     * Create a new proposal for this hotel
     *
     * @param int $hotelId
     *
     * @return mixed
     */
    public function storeForRequestAndHotel($proposalRequestId, $hotelId);

    /**
     * Add a user to the proposal
     *
     * @param int $proposalId
     * @param int $userId
     * @param array $contactInfo
     *
     * @return mixed
     */
    public function addUser($proposalId, $userId, $contactInfo = []);

    /**
     * Remove user from proposal
     *
     * @param int $proposalId
     * @param int $userId
     *
     * @return mixed
     */
    public function removeUser($proposalId, $userId);

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
    public function decline($proposalId, $eventDateRangeId, $userId, $userType, $reason = null);

    /**
     * Reset the decline for a proposal
     *
     * @param int $proposalId
     * @param int $eventDateRangeId
     * @param string $userType
     *
     * @return mixed
     */
    public function resetDecline($proposalId, $eventDateRangeId, $userType);

    /**
     * Mark a proposal "submitted"
     *
     * @param int $proposalId
     * @param int $eventDateRangeId
     * @param int $userId
     *
     * @return mixed
     */
    public function submit($proposalId, $eventDateRangeId, $userId);

    /**
     * Create a date range for this proposal
     *
     * @param int $proposalId
     * @param int $eventDateRangeId
     *
     * @return mixed
     */
    public function initializeDateRange($proposalId, $eventDateRangeId);

    /**
     * Update date range data for proposal
     *
     * @param int $proposalId
     * @param int $eventDateRangeId
     * @param array $data
     *
     * @return mixed
     */
    public function updateDateRange($proposalId, $eventDateRangeId, $data = []);

    /**
     * Can this hotel user access the proposal?
     *
     * @param int $userId
     * @param int $proposalId
     *
     * @return mixed
     */
    public function userBelongsToHotelOnProposal($userId, $proposalId);

    /**
     * Update the honor_bid_until values for all of this licensee's proposals by offet value
     *
     * @param int $licenseeId
     * @param int $offset
     *
     * @return mixed
     */
    public function adjustTimezones($licenseeId, $offset);

    /**
     * Does this proposal belong to this licensee?
     *
     * @param int $proposalId
     * @param int $licenseeId
     *
     * @return mixed
     */
    public function belongsToLicensee($proposalId, $licenseeId);
}
