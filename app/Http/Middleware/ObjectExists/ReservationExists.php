<?php

namespace App\Http\Middleware\ObjectExists;

use App\Repositories\Contracts\LicenseeTermRepositoryInterface;
use App\Repositories\Contracts\ReservationRepositoryInterface;
use Closure;

class ReservationExists extends ObjectExists
{
    /**
     * @var string
     */
    protected $errorMessage = 'This Reservation does not exist.';

    /**
     * @var string
     */
    protected $idKey = 'reservationId';

    /**
     * Repository class
     *
     * @var string
     */
    protected $repoClass = ReservationRepositoryInterface::class;
}
