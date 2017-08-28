<?php

namespace App\Repositories\Contracts;

interface BrandRepositoryInterface
{
    /**
     * Get all brands without a parent
     *
     * @return mixed
     */
    public function allParentBrands();

    /**
     * Given a list of IDs, return a list that includes those IDs plus the IDs of any children
     *
     * @param array $brandIds
     * @return mixed
     */
    public function getWhitelist($brandIds = []);
}