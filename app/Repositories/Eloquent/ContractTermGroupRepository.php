<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\ContractTermGroupRepositoryInterface;

class ContractTermGroupRepository extends Repository implements ContractTermGroupRepositoryInterface
{
    /**
     * Get all term groups for this contract
     *
     * @param int $contractId
     *
     * @return mixed
     */
    public function allForContract($contractId)
    {
        return $this->model
            ->whereContractId($contractId)
            ->get();
    }

    /**
     * Create new term group for contract
     *
     * @param int $contractId
     * @param string $name
     *
     * @return mixed
     */
    public function storeForContract($contractId, $name)
    {
        return $this->store([
            'contract_id'   => $contractId,
            'name'          => $name,
        ]);
    }

    /**
     * Remove a Term Group and all Terms
     *
     * @param int $groupId
     *
     * @return mixed
     */
    public function removeGroupAndTerms($groupId)
    {
        $this->find($groupId)
            ->terms()
            ->delete();

        $this->delete($groupId);
    }
}