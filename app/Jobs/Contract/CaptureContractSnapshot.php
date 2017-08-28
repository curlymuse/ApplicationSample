<?php

namespace App\Jobs\Contract;

use App\Events\Admin\Contract\ContractSnapshotWasCaptured;
use App\Repositories\Contracts\ContractRepositoryInterface;
use App\Transformers\Contract\ContractTransformer;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CaptureContractSnapshot implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    private $contractId;

    /**
     * Create a new job instance.
     *
     * @param int $contractId
     */
    public function __construct($contractId)
    {
        $this->contractId = $contractId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ContractRepositoryInterface $contractRepo, ContractTransformer $transformer)
    {
        $contract = $contractRepo->find($this->contractId);

        $contractRepo->update(
            $this->contractId,
            [
                'snapshot'  => json_encode($transformer->transform($contract)),
            ]
        );

        event(
            new ContractSnapshotWasCaptured(
                $contract
            )
        );
    }
}
