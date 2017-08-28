<?php

namespace App\Repositories\Contracts;

interface HotelSearchRepositoryInterface
{
    /**
     * Return a list of hotels matching these criteria
     *
     * @param array $terms
     */
    public function search($terms = []);
}