<?php

namespace Tests\Unit\Jobs\ChangeOrder;

use App\Events\ChangeOrder\ChangeOrderItemWasAccepted;
use App\Jobs\ChangeOrder\AcceptChangeOrderItem;
use App\Repositories\Contracts\ChangeOrderRepositoryInterface;
use App\Support\ChangeOrderProcessor;
use Tests\TestCase;

/**
 * Class AcceptChangeOrderItemTest
 *
 * @coversBaseClass App\Jobs\ChangeOrder\AcceptChangeOrderItem
 */
class AcceptChangeOrderItemTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var \App\Repositories\Contracts\ChangeOrderRepositoryInterface
     */
    private $changeOrderRepo;

    /**
     * @var \App\Support\ChangeOrderProcessor
     */
    private $processor;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->changeOrderRepo = $this->expectsRepository(ChangeOrderRepositoryInterface::class);
        $this->processor = $this->mock(ChangeOrderProcessor::class);
    }

    public function test_handle()
    {
        $userId = $this->faker->numberBetween(1, 1000);
        $changeOrderId = $this->faker->numberBetween(1, 1000);

        $this->processor->shouldReceive('process')
            ->once()
            ->with(
                $changeOrderId
            );

        $this->changeOrderRepo->shouldReceive('accept')
            ->once()
            ->with(
                $changeOrderId,
                $userId
            );

        $this->expectsEvents(ChangeOrderItemWasAccepted::class);

        dispatch(
            new AcceptChangeOrderItem(
                $changeOrderId,
                $userId
            )
        );
    }
}