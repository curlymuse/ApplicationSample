<?php

namespace Tests\Unit\Jobs\ChangeOrder;

use App\Events\ChangeOrder\ChangeOrderItemWasDeclined;
use App\Jobs\ChangeOrder\DeclineChangeOrderItem;
use App\Repositories\Contracts\ChangeOrderRepositoryInterface;
use Tests\TestCase;

/**
 * Class DeclineChangeOrderItemTest
 *
 * @coversBaseClass App\Jobs\ChangeOrder\DeclineChangeOrderItem
 */
class DeclineChangeOrderItemTest extends TestCase
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
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->changeOrderRepo = $this->expectsRepository(ChangeOrderRepositoryInterface::class);
    }

    public function test_handle()
    {
        $userId = $this->faker->numberBetween(1, 1000);
        $changeOrderId = $this->faker->numberBetween(1, 1000);
        $reason = $this->faker->sentence;

        $this->changeOrderRepo->shouldReceive('decline')
            ->once()
            ->with(
                $changeOrderId,
                $userId,
                $reason
            );

        $this->expectsEvents(ChangeOrderItemWasDeclined::class);

        dispatch(
            new DeclineChangeOrderItem(
                $changeOrderId,
                $userId,
                $reason
            )
        );
    }
}