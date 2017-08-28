<?php

namespace Tests\Unit\Jobs\Contract;

use App\Events\Admin\Contract\ContractSnapshotWasCaptured;
use App\Jobs\Contract\CaptureContractSnapshot;
use App\Models\Contract;
use App\Repositories\Contracts\ContractRepositoryInterface;
use App\Transformers\Contract\ContractTransformer;
use Tests\TestCase;

/**
 * Class CaptureContractSnapshotTest
 *
 * @coversBaseClass App\Jobs\Contract\CaptureContractSnapshot
 */
class CaptureContractSnapshotTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var \App\Repositories\Contracts\ContractRepositoryInterface
     */
    private $contractRepo;

    /**
     * @var \App\Transformers\Contract\ContractTransformer
     */
    private $transformer;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->contractRepo = $this->expectsRepository(ContractRepositoryInterface::class);
        $this->transformer = $this->mock(ContractTransformer::class);
    }

    public function test_handle()
    {
        $dummyArray = $this->getDummyArray();
        $contract = factory(Contract::class)->create();

        $this->contractRepo->shouldReceive('find')
            ->once()
            ->with($contract->id)
            ->andReturn($contract);

        $this->transformer->shouldReceive('transform')
            ->once()
            ->with($contract)
            ->andReturn($dummyArray);

        $this->contractRepo->shouldReceive('update')
            ->once()
            ->with(
                $contract->id,
                [
                    'snapshot'  => json_encode($dummyArray),
                ]
            );

        $this->expectsEvents(ContractSnapshotWasCaptured::class);

        dispatch(
            new CaptureContractSnapshot(
                $contract->id
            )
        );
    }
}