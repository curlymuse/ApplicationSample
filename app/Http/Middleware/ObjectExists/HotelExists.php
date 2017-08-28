<?php

namespace App\Http\Middleware\ObjectExists;

use App\Repositories\Contracts\HotelRepositoryInterface;
use Closure;

class HotelExists extends ObjectExists
{
    /**
     * @var string
     */
    protected $errorMessage = 'This Hotel does not exist.';

    /**
     * @var string
     */
    protected $idKey = 'hotelId';

    /**
     * Repository class
     *
     * @var string
     */
    protected $repoClass = HotelRepositoryInterface::class;
}
