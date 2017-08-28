<?php

namespace App\Repositories\Contracts;

interface ClauseRepositoryInterface
{
    /**
     * Get all Clauses for Licensee
     *
     * @param int $licenseeId
     *
     * @return mixed
     */
    public function allForLicensee($licenseeId);

    /**
     * Create a new Clause for this Licensee
     *
     * @param int $licenseeId
     * @param array $attributes
     *
     * @return mixed
     */
    public function storeForLicensee($licenseeId, $attributes = []);

    /**
     * Does this clause belong to this licensee?
     *
     * @param int $clauseId
     * @param int $licenseeId
     *
     * @return mixed
     */
    public function belongsToLicensee($clauseId, $licenseeId);
}