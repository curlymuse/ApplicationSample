<?php

namespace App\Repositories\Contracts;

interface ProposalRequestRepositoryInterface
{
    /**
     * Create a new ProposalRequest for the given Event
     *
     * @param int $eventId
     * @param array $attributes
     *
     * @return mixed
     */
    public function storeForEvent($eventId, $attributes = []);

    /**
     * Does this ProposalRequest have at least one Proposal?
     *
     * @param int $requestId
     *
     * @return mixed
     */
    public function hasProposals($requestId);

    /**
     * Does this PR have disbursements?
     *
     * @param int $requestId
     *
     * @return mixed
     */
    public function hasDisbursements($requestId);

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
    public function addUser($requestId, $userId, $contactInfo = []);

    /**
     * Delete all users from proposal request (use for syncing purposes)
     *
     * @param int $requestId
     *
     * @return mixed
     */
    public function clearUsers($requestId);

    /**
     * Get all proposal requests for hotel
     *
     * @param int $hotelId
     *
     * @return Collection
     */
    public function allForHotel($hotelId);

    /**
     * Get all proposal requests belonging to a licensee
     *
     * @param int $licenseeId
     *
     * @return mixed
     */
    public function allForLicensee($licenseeId);

    /**
     * All PRs for a licensee without any date ranges attached
     *
     * @param int $licenseeId
     *
     * @return mixed
     */
    public function allForLicenseeWithoutDateRanges($licenseeId);

    /**
     * @param $licenseeId
     *
     * @param null|string $startDate
     * @param null|string $endDate
     *
     * @return mixed
     */
    public function allRfpStageForLicenseeWithDateRange($licenseeId, $startDate = null, $endDate = null);

    /**
     * @param $licenseeId
     *
     * @param null|string $startDate
     * @param null|string $endDate
     *
     * @return mixed
     */
    public function allContractStageForLicenseeWithDateRange($licenseeId, $startDate = null, $endDate = null);

    /**
     * Get all proposal requests belonging to a licensee
     *
     * @param int $licenseeId
     * @param null $startDate
     * @param null $endDate
     *
     * @return mixed
     */
    public function allForLicenseeWithDateRange($licenseeId, $startDate = null, $endDate = null);

    /**
     * Get all proposal requests belonging to hotels of a GSO's brands
     *
     * @param int $userId
     * @param null $startDate
     * @param null $endDate
     *
     * @return mixed
     */
    public function allForGSOWithDateRange($userId, $startDate = null, $endDate = null);

    /**
     * Get all proposal requests belonging to a hotelier user's hotels
     *
     * @param int $userId
     * @param null $startDate
     * @param null $endDate
     *
     * @return mixed
     */
    public function allForHotelierWithDateRange($userId, $startDate = null, $endDate = null);

    /**
     * Get all unread Proposal Requests for this User, including the hash associating the user
     * with the PR
     *
     * @param int $userId
     *
     * @return mixed
     */
    public function allUnreadForUser($userId);

    /**
     * Duplicate this proposal request and attach to event
     *
     * @param int $requestId
     * @param int $eventId
     *
     * @return mixed
     */
    public function duplicateWithEventId($requestId, $eventId);

    /**
     * All requests for given hotel with upcoming cutoff dates,
     * and no signed contract
     *
     * @param int $hotelId
     *
     * @return mixed
     */
    public function withUpcomingCutoffDatesForHotel($hotelId);

    /**
     * Sync the passed in location IDs with the given Proposal Request
     *
     * @param int $requestId
     * @param array $locationIds
     *
     * @return mixed
     */
    public function syncEventLocations($requestId, $locationIds = []);

    /**
     * Sync reservation methods
     *
     * @param int $requestId
     * @param array $methodIds
     *
     * @return mixed
     */
    public function syncReservationMethods($requestId, $methodIds = []);

    /**
     * Sync reservation methods
     *
     * @param int $requestId
     * @param array $methodIds
     *
     * @return mixed
     */
    public function syncPaymentMethods($requestId, $methodIds = []);

    /**
     * Does this Proposal Request belong to this Licensee?
     *
     * @param int $requestId
     * @param int $licenseeId
     *
     * @return bool
     */
    public function belongsToLicensee($requestId, $licenseeId);

    /**
     * Adjust timezones on cutoff_date for all PRs for this licensee
     *
     * @param int $licenseeId
     * @param int $offset
     *
     * @return mixed
     */
    public function adjustTimezones($licenseeId, $offset);
}
