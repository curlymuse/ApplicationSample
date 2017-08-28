<?php

namespace Tests\Unit\Jobs\ChangeOrder;

use App\Events\ChangeOrder\OfflineChangeOrderSetWasProcessed;
use App\Jobs\ChangeOrder\ProcessOfflineChangeOrderSet;
use App\Models\ChangeOrder;
use App\Models\Contract;
use App\Repositories\Contracts\ChangeOrderRepositoryInterface;
use App\Support\ChangeOrderParser;
use App\Support\ChangeOrderProcessor;
use Tests\TestCase;

/**
 * Class ProcessOfflineChangeOrderSetTest
 *
 * @coversBaseClass App\Jobs\ChangeOrder\ProcessOfflineChangeOrderSet
 */
class ProcessOfflineChangeOrderSetTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var \App\Support\ChangeOrderParser
     */
    private $parser;

    /**
     * @var \App\Support\ChangeOrderProcessor
     */
    private $processor;

    /**
     * @var \App\Repositories\Contracts\ChangeOrderRepositoryInterface
     */
    private $changeOrderRepo;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->parser = $this->mock(ChangeOrderParser::class);
        $this->processor = $this->mock(ChangeOrderProcessor::class);
        $this->changeOrderRepo = $this->expectsRepository(ChangeOrderRepositoryInterface::class);
    }

    public function test_handle()
    {
        $userId = $this->faker->numberBetween(1, 1000);
        $userType = $this->faker->randomElement(['licensee', 'hotel']);
        $contract = factory(Contract::class)->create();
        $addAttachments = [];
        $removeAttachments = [];

        $inputData = $this->getDummyArray();
        $labels = $this->getDummyArray();
        $changeOrder = factory(ChangeOrder::class)->create();

        for ($i = 0; $i < $this->faker->numberBetween(1, 2); $i++) {
            $changeOrder->children()->save(
                factory(ChangeOrder::class)->create()
            );
        }

        $changes = $this->getDummyArray();

        $this->parser->shouldReceive('parseChanges')
            ->once()
            ->with(
                $contract->id,
                $inputData
            )
            ->andReturn($changes);

        $this->changeOrderRepo->shouldReceive('createSet')
            ->once()
            ->with(
                $contract->id,
                $userId,
                $userType,
                $changes,
                $labels
            )
            ->andReturn($changeOrder);

        $this->processor->shouldReceive('process')
            ->times($changeOrder->children()->count());

        $this->changeOrderRepo->shouldReceive('accept')
            ->times($changeOrder->children()->count());

        $this->expectsEvents(OfflineChangeOrderSetWasProcessed::class);

        dispatch(
            new ProcessOfflineChangeOrderSet(
                $contract->id,
                $inputData,
                $userId,
                $userType,
                $labels
            )
        );
    }
}