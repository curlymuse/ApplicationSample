<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\BrandRepositoryInterface;

class BrandRepository extends Repository implements BrandRepositoryInterface
{
    /**
     * Get all brands without a parent
     *
     * @return mixed
     */
    public function allParentBrands()
    {
        return $this->model
            ->whereNull('parent_id')
            ->with('subBrands')
            ->get();
    }

    /**
     * Given a list of IDs, return a list that includes those IDs plus the IDs of any children
     *
     * @param array $brandIds
     * @return mixed
     */
    public function getWhitelist($brandIds = [])
    {
        $brands = $this->model
            ->whereIn('id', $brandIds)
            ->with('subBrands')
            ->get();

        $ids = $brands->pluck('id');

        foreach ($brands as $brand) {
            foreach ($brand->subBrands as $subBrand) {
                $ids[] = $subBrand->id;
            }
        }

        return $ids;
    }
}