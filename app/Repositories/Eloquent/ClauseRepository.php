<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\ClauseRepositoryInterface;

class ClauseRepository extends Repository implements ClauseRepositoryInterface
{
    /**
     * Get all Clauses for Licensee
     *
     * @param int $licenseeId
     *
     * @return mixed
     */
    public function allForLicensee($licenseeId)
    {
        return $this->model
            ->whereLicenseeId($licenseeId)
            ->get();
    }

    /**
     * Create a new Clause for this Licensee
     *
     * @param int $licenseeId
     * @param array $attributes
     *
     * @return mixed
     */
    public function storeForLicensee($licenseeId, $attributes = [])
    {
        return $this->store(
            collect($attributes)->merge([
                'licensee_id'   => $licenseeId,
            ])->toArray()
        );
    }

    /**
     * Does this clause belong to this licensee?
     *
     * @param int $clauseId
     * @param int $licenseeId
     *
     * @return mixed
     */
    public function belongsToLicensee($clauseId, $licenseeId)
    {
        return $this->model
            ->whereId($clauseId)
            ->whereLicenseeId($licenseeId)
            ->exists();
    }
}