<?php

namespace App\Http\Middleware;

use App\Exceptions\Middleware\InvalidObjectStateException;
use App\Repositories\Contracts\ContractRepositoryInterface;
use Closure;

class ContractIsNotClientOwned
{
    /**
     * @var ContractRepositoryInterface
     */
    private $contractRepo;

    /**
     * ContractIsNotClientOwned constructor.
     *
     * @param ContractRepositoryInterface $contractRepo
     */
    public function __construct(ContractRepositoryInterface $contractRepo)
    {
        $this->contractRepo = $contractRepo;
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
        if ($this->contractRepo->isClientOwned($request->route('contractId'))) {
            throw new InvalidObjectStateException('This action is not possible, because this contract is owned by the client.');
        }

        return $next($request);
    }
}
