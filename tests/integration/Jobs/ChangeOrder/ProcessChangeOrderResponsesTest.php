<?php

namespace Tests\Integration\Jobs\ChangeOrder;

use App\Events\ChangeOrder\ChangeOrderSetWasProcessed;
use App\Jobs\ChangeOrder\ProcessChangeOrderResponses;
use App\Models\ChangeOrder;
use App\Models\User;
use App\Repositories\Contracts\ChangeOrderRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Tests\TestCase;

/**
 * Class ProcessChangeOrderResponsesTest
 *
 * @coversBaseClass App\Jobs\ChangeOrder\ProcessChangeOrderResponses
 */
class ProcessChangeOrderResponsesTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var \App\Models\ChangeOrder
     */
    protected $changeOrderSet;

    /**
     * @var \App\Models\ChangeOrder
     */
    protected $declineChangeOrder;

    /**
     * @var \App\Models\ChangeOrder
     */
    protected $acceptChangeOrder;

    /**
     * @var \App\Repositories\Contracts\ChangeOrderRepositoryInterface
     */
    private $changeOrderRepo;

    /**
     * @var \App\Repositories\Contracts\UserRepositoryInterface
     */
    private $userRepo;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->changeOrderSet = factory(ChangeOrder::class)->create();
        $this->user = factory(User::class)->create();

        $this->declineChangeOrder = factory(ChangeOrder::class)->create([
            'contract_id'   => $this->changeOrderSet->contract_id,
            'parent_id'   => $this->changeOrderSet->id,
            'initiated_by_user' => $this->changeOrderSet->initiated_by_user,
        ]);
        $this->acceptChangeOrder = factory(ChangeOrder::class)->create([
            'contract_id'   => $this->changeOrderSet->contract_id,
            'parent_id'   => $this->changeOrderSet->id,
            'initiated_by_user' => $this->changeOrderSet->initiated_by_user,
        ]);

        $this->changeOrderRepo = $this->expectsRepository(ChangeOrderRepositoryInterface::class);
        $this->userRepo = $this->expectsRepository(UserRepositoryInterface::class);
    }

    public function test_handle()
    {
        $changes = [
            [
                'id'    => $this->declineChangeOrder->id,
                'accepted'   => 0,
                'reason'    => $this->faker->sentence,
            ],
            [
                'id'    => $this->acceptChangeOrder->id,
                'accepted'   => 1,
            ]
        ];

        $this->userRepo->shouldReceive('find');
        $this->changeOrderRepo->shouldReceive('find')
            ->andReturn(factory(ChangeOrder::class)->create());

        $this->changeOrderRepo->shouldReceive('decline')
            ->once()
            ->with(
                $this->declineChangeOrder->id,
                $this->user->id,
                $changes[0]['reason']
            );

        $this->changeOrderRepo->shouldReceive('accept')
            ->once()
            ->with(
                $this->acceptChangeOrder->id,
                $this->user->id
            );

        $this->expectsEvents(ChangeOrderSetWasProcessed::class);

        dispatch(
            new ProcessChangeOrderResponses(
                $this->changeOrderSet->id,
                $this->user->id,
                $changes
            )
        );
    }
}