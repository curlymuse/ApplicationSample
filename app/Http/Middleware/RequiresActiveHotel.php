<?php

namespace App\Http\Middleware;

use App\Exceptions\Middleware\NotAuthorizedException;
use App\Repositories\Contracts\HotelRepositoryInterface;
use Closure;

class RequiresActiveHotel
{
    /**
     * @var HotelRepositoryInterface
     */
    private $hotelRepo;

    /**
     * @param HotelRepositoryInterface $hotelRepo
     */
    public function __construct(HotelRepositoryInterface $hotelRepo)
    {
        $this->hotelRepo = $hotelRepo;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function handle($request, Closure $next)
    {
        if (! hotelId()) {
            throw new NotAuthorizedException('No active hotel set.');
        }

        $this->hotel = $this->hotelRepo->find(hotelId());

        return $next($request);
    }
}
