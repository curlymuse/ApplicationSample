<?php

namespace App\Repositories\Contracts;

interface ContractTermGroupRepositoryInterface
{
    /**
     * Get all term groups for this contract
     *
     * @param int $contractId
     *
     * @return mixed
     */
    public function allForContract($contractId);

    /**
     * Create new term group for contract
     *
     * @param int $contractId
     * @param string $name
     *
     * @return mixed
     */
    public function storeForContract($contractId, $name);

    /**
     * Remove a Term Group and all Terms
     *
     * @param int $groupId
     *
     * @return mixed
     */
    public function removeGroupAndTerms($groupId);
}