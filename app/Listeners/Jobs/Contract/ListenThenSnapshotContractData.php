<?php

namespace App\Listeners\Jobs\Contract;

use App\Events\Admin\Contract\ContractWasAccepted;
use App\Jobs\Contract\CaptureContractSnapshot;
use App\Repositories\Contracts\ContractRepositoryInterface;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ListenThenSnapshotContractData
{
    /**
     * @var ContractRepositoryInterface
     */
    private $contractRepo;

    /**
     * Create the event listener.
     *
     * @param ContractRepositoryInterface $contractRepo
     */
    public function __construct(ContractRepositoryInterface $contractRepo)
    {
        $this->contractRepo = $contractRepo;
    }

    /**
     * Handle the event.
     *
     * @param  ContractWasAccepted  $event
     * @return void
     */
    public function handle(ContractWasAccepted $event)
    {
        $contract = $this->contractRepo->find($event->getContractId());

        if ((bool)$contract->accepted_by_hotel_at && (bool)$contract->accepted_by_owner_at) {
            dispatch(
                new CaptureContractSnapshot(
                    $contract->id
                )
            );
        }
    }
}
