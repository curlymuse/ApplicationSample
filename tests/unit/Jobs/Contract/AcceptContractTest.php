<?php

namespace Tests\Unit\Jobs\Contract;

use App\Events\Admin\Contract\ContractWasAccepted;
use App\JobPolicies\Contract\AcceptContractPolicy;
use App\Jobs\Contract\AcceptContract;
use App\Models\Contract;
use App\Repositories\Contracts\ContractRepositoryInterface;
use Tests\TestCase;

/**
 * Class AcceptContractTest
 *
 * @coversBaseClass App\Jobs\Contract\AcceptContract
 */
class AcceptContractTest extends TestCase
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
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->contractRepo = $this->expectsRepository(ContractRepositoryInterface::class);
    }

    public function test_handle_hotel()
    {
        $contractId = $this->faker->numberBetween(1, 1000);
        $userId = $this->faker->numberBetween(1, 1000);
        $signature = $this->faker->name;

        $this->contractRepo->shouldReceive('find')
            ->once()
            ->with(
                $contractId
            )
            ->andReturn(factory(Contract::class)->make());

        $this->contractRepo->shouldReceive('acceptForHotel')
            ->once()
            ->with(
                $contractId,
                $userId,
                $signature
            );

        $this->expectsPolicy(AcceptContractPolicy::class);
        $this->expectsEvents(ContractWasAccepted::class);

        dispatch(
            new AcceptContract(
                $contractId,
                $userId,
                'hotel',
                $signature
            )
        );
    }

    public function test_handle_owner()
    {
        $contractId = $this->faker->numberBetween(1, 1000);
        $userId = $this->faker->numberBetween(1, 1000);
        $signature = $this->faker->name;

        $this->contractRepo->shouldReceive('find')
            ->once()
            ->with(
                $contractId
            )
            ->andReturn(factory(Contract::class)->make());

        $this->contractRepo->shouldReceive('acceptForOwner')
            ->once()
            ->with(
                $contractId,
                $userId,
                $signature
            );

        $this->expectsPolicy(AcceptContractPolicy::class);
        $this->expectsEvents(ContractWasAccepted::class);

        dispatch(
            new AcceptContract(
                $contractId,
                $userId,
                'owner',
                $signature
            )
        );
    }
}