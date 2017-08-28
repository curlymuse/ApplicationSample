<?php

namespace Tests\Unit\Jobs\ChangeOrder;

use App\Events\ChangeOrder\ChangeOrderWasCreated;
use App\JobPolicies\ChangeOrder\CreateChangeOrderSetPolicy;
use App\Jobs\ChangeOrder\CreateChangeOrderSet;
use App\Models\ChangeOrder;
use App\Models\Contract;
use App\Models\User;
use App\Repositories\Contracts\ChangeOrderRepositoryInterface;
use App\Repositories\Contracts\ContractRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Support\ChangeOrderParser;
use Tests\TestCase;

/**
 * Class CreateChangeOrderSetTest
 *
 * @coversBaseClass App\Jobs\ChangeOrder\CreateChangeOrderSet
 */
class CreateChangeOrderSetTest extends TestCase
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
     * @var \App\Repositories\Contracts\ChangeOrderRepositoryInterface
     */
    private $changeOrderRepo;

    /**
     * @var \App\Repositories\Contracts\UserRepositoryInterface
     */
    private $userRepo;

    /**
     * @var \App\Support\ChangeOrderParser
     */
    private $parser;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->contractRepo = $this->expectsRepository(ContractRepositoryInterface::class);
        $this->changeOrderRepo = $this->expectsRepository(ChangeOrderRepositoryInterface::class);
        $this->userRepo = $this->expectsRepository(UserRepositoryInterface::class);
        $this->parser = $this->mock(ChangeOrderParser::class);
    }

    public function test_handle()
    {
        $userId = $this->faker->numberBetween(1, 1000);
        $userType = $this->faker->randomElement(['licensee', 'hotel']);
        $contract = factory(Contract::class)->create();
        $addAttachments = [];
        $removeAttachments = [];
        $reason = $this->faker->sentence;

        $inputData = $this->getDummyArray();
        $labels = $this->getDummyArray();
        $changeOrder = factory(ChangeOrder::class)->create();

        $this->userRepo->shouldReceive('find')
            ->once()
            ->with($userId)
            ->andReturn(factory(User::class)->create());

        $this->contractRepo->shouldReceive('find')
            ->once()
            ->with($contract->id)
            ->andReturn($contract);

        $dummyChangeData = $this->getDummyArray();
        $this->parser->shouldReceive('parseChanges')
            ->once()
            ->with(
                $contract->id,
                $inputData,
                $addAttachments,
                $removeAttachments
            )
            ->andReturn($dummyChangeData);

        $this->changeOrderRepo->shouldReceive('createSet')
            ->once()
            ->with(
                $contract->id,
                $userId,
                $userType,
                $dummyChangeData,
                $labels,
                $reason
            )
            ->andReturn($changeOrder);

        $this->expectsEvents(ChangeOrderWasCreated::class);
        $this->expectsPolicy(CreateChangeOrderSetPolicy::class);

        dispatch(
            new CreateChangeOrderSet(
                $contract->id,
                $inputData,
                $addAttachments,
                $removeAttachments,
                $userId,
                $userType,
                $labels,
                $reason
            )
        );
    }
}