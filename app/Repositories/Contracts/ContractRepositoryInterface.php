<?php

namespace App\Repositories\Contracts;

use App\Models\Proposal;

interface ContractRepositoryInterface
{
    /**
     * Get all Contracts for this Proposal Request
     *
     * @param int $requestId
     *
     * @return mixed
     */
    public function allForProposalRequest($requestId);

    /**
     * Get all Contracts for this Licensee
     *
     * @param int $licenseeId
     *
     * @return mixed
     */
    public function allForLicensee($licenseeId);

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
    public function allForHotelier($userId, $startDate = null, $endDate = null, $proposalRequestId = null);

    /**
     * Get all Contracts associated with this GSO user's account
     *
     * @param int $userId
     * @param string|null $startDate
     * @param string|null $endDate
     * @param int|null $proposalRequestId
     *
     * @return mixed
     */
    public function allForGSO($userId, $startDate = null, $endDate = null, $proposalRequestId = null);

    /**
     * Does this contract have pending change orders
     *
     * @param int $contractId
     *
     * @return bool
     */
    public function hasPendingChangeOrders($contractId);

    /**
     * Does this contract have change orders?
     *
     * @param int $contractId
     *
     * @return mixed
     */
    public function hasChangeOrders($contractId);

    /**
     * Is the latest change order on this contract accepted fully?
     *
     * @param int $contractId
     *
     * @return mixed
     */
    public function hasLatestChangeOrderSetAccepted($contractId);

    /**
     * Validate client hash
     *
     * @param int $contractId
     * @param string $clientHash
     *
     * @return mixed
     */
    public function validateClientHash($contractId, $clientHash);

    /**
     * Create a new Contract, using data from a Proposal
     *
     * @param Proposal $proposal
     * @param int $eventDateRangeId
     *
     * @return Contract
     */
    public function initializeWithProposal(Proposal $proposal, $eventDateRangeId);

    /**
     * Is this contract client-owned?
     *
     * @param int $contractId
     *
     * @return mixed
     */
    public function isClientOwned($contractId);

    /**
     * Is this contract client-owned?
     *
     * @param int $contractId
     *
     * @return mixed
     */
    public function isOffline($contractId);

    /**
     * Remove this reservation method from the contract
     *
     * @param int $contractId
     * @param int $methodId
     *
     * @return mixed
     */
    public function removeReservationMethod($contractId, $methodId);

    /**
     * Remove this payment method from the contract
     *
     * @param int $contractId
     * @param int $methodId
     *
     * @return mixed
     */
    public function removePaymentMethod($contractId, $methodId);

    /**
     * Add this reservation method to the contract
     *
     * @param int $contractId
     * @param int $methodId
     *
     * @return mixed
     */
    public function addReservationMethod($contractId, $methodId);

    /**
     * Add this payment method to the contract
     *
     * @param int $contractId
     * @param int $methodId
     *
     * @return mixed
     */
    public function addPaymentMethod($contractId, $methodId);

    /**
     * Make this Contract offline
     *
     * @param int $contractId
     *
     * @return mixed
     */
    public function makeOffline($contractId);

    /**
     * Reset a contract's declined status for a licensee
     *
     * @param int $contractId
     *
     * @return mixed
     */
    public function resetDeclineForOwner($contractId);

    /**
     * Mark a contract "declined" for the licensee
     *
     * @param int $contractId
     * @param int $userId
     * @param null $reason
     *
     * @return mixed
     */
    public function declineForOwner($contractId, $userId, $reason = null);

    /**
     * Mark a contract "declined" for the hotel
     *
     * @param int $contractId
     * @param int $userId
     * @param null $reason
     *
     * @return mixed
     */
    public function declineForHotel($contractId, $userId, $reason = null);

    /**
     * Accept contract on behalf of hotel
     *
     * @param int $contractId
     * @param int $userId
     * @param string $signature
     *
     * @return mixed
     */
    public function acceptForHotel($contractId, $userId, $signature);

    /**
     * Accept contract on behalf of licensee
     *
     * @param int $contractId
     * @param int $userId
     * @param string $signature
     *
     * @return mixed
     */
    public function acceptForOwner($contractId, $userId, $signature);

    /**
     * Revoke client ownership and remove hash
     *
     * @param int $contractId
     *
     * @return mixed
     */
    public function revokeClientOwnership($contractId);

    /**
     * Transfer ownership to Client and assign client hash
     *
     * @param int $contractId
     *
     * @return mixed
     */
    public function transferOwnershipToClient($contractId);

    /**
     * Can this hotel user access the contract?
     *
     * @param int $userId
     * @param int $contractId
     *
     * @return mixed
     */
    public function userBelongsToHotelOnContract($userId, $contractId);

    /**
     * Does this contract belong to this proposal request?
     *
     * @param int $contractId
     * @param int $requestId
     *
     * @return mixed
     */
    public function belongsToProposalRequest($contractId, $requestId);

    /**
     * Does this contract belong to this licensee?
     *
     * @param int $contractId
     * @param int $licenseeId
     *
     * @return mixed
     */
    public function belongsToLicensee($contractId, $licenseeId);

    /**
     * Does this contract belong to this hotel?
     *
     * @param int $contractId
     * @param int $hotelId
     *
     * @return mixed
     */
    public function belongsToHotel($contractId, $hotelId);
}
