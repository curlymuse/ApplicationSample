<?php

namespace App\Http\Middleware\BelongsTo;


use App\Exceptions\Middleware\IncorrectAssociationException;
use App\Repositories\Contracts\ContractRepositoryInterface;
use App\Repositories\Contracts\ReservationRepositoryInterface;
use Closure;

class ReservationBelongsToContract
{
    /**
     * @var ReservationRepositoryInterface
     */
    private $reservationRepo;

    /**
     * ReservationBelongsToContract constructor.
     * @param ReservationRepositoryInterface $reservationRepo
     */
    public function __construct(ReservationRepositoryInterface $reservationRepo)
    {
        $this->reservationRepo = $reservationRepo;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $reservation = $this->reservationRepo->find($request->route('reservationId'));

        if ($reservation->roomSets()->first()->contract_id != $request->route('contractId')) {
            throw new IncorrectAssociationException('This reservation does not belong to this contract.');
        }

        return $next($request);
    }
}