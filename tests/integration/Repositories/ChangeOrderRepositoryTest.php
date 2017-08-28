<?php

namespace Tests\Integration\Repositories;

use App\Models\ChangeOrder;
use App\Models\Contract;
use App\Models\RoomSet;
use App\Models\User;
use App\Repositories\Contracts\ChangeOrderRepositoryInterface;
use App\Repositories\Eloquent\ChangeOrderRepository;
use Carbon\Carbon;
use Tests\TestCase;

/**
 * Class ChangeOrderRepositoryTest
 *
 * @coversBaseClass \App\Repositories\ChangeOrderRepository
 */
class ChangeOrderRepositoryTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var \App\Repositories\Contracts\ChangeOrderRepositoryInterface
     */
    private $repo;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->repo = app(ChangeOrderRepositoryInterface::class);
    }

    /**
     * @covers ::allForContract
     */
    public function test_allForContract()
    {
        $includeChangeOrder = factory(ChangeOrder::class)->create();
        $excludeChangeOrder = factory(ChangeOrder::class)->create();
        $excludeChangeOrderItem = factory(ChangeOrder::class)->create([
            'contract_id'   => $includeChangeOrder->contract_id,
            'parent_id' => $includeChangeOrder->id,
        ]);

        $results = $this->repo->allForContract($includeChangeOrder->contract_id);

        $this->assertContains($includeChangeOrder->id, $results->pluck('id'));
        $this->assertNotContains($excludeChangeOrder->id, $results->pluck('id'));
        $this->assertNotContains($excludeChangeOrderItem->id, $results->pluck('id'));
    }

    /**
     * @covers ::decline
     */
    public function test_decline()
    {
        $user = factory(User::class)->create();
        $changeOrder = factory(ChangeOrder::class)->create();
        $reason = $this->faker->sentence;

        $this->repo->decline(
            $changeOrder->id,
            $user->id,
            $reason
        );

        $this->assertTrue(
            ChangeOrder::whereId($changeOrder->id)
                ->whereDeclinedByUser($user->id)
                ->whereDeclinedBecause($reason)
                ->whereNotNull('declined_at')
                ->exists()
        );
    }

    /**
     * @covers ::accept
     */
    public function test_accept()
    {
        $user = factory(User::class)->create();
        $changeOrder = factory(ChangeOrder::class)->create();

        $this->repo->accept(
            $changeOrder->id,
            $user->id
        );

        $this->assertTrue(
            ChangeOrder::whereId($changeOrder->id)
                ->whereAcceptedByUser($user->id)
                ->whereNotNull('accepted_at')
                ->exists()
        );
    }

    /**
     * @covers ::createSet
     */
    public function test_createSet()
    {
        $contract = factory(Contract::class)->create();
        $user = factory(User::class)->create();
        $userType = $this->faker->randomElement(['hotel', 'licensee']);
        $reason = $this->faker->sentence;

        $changes = [];
        $labels = [];
        for ($i = 0; $i < $this->faker->numberBetween(1, 3); $i++) {
            $change =[
                'key'   => $this->faker->word,
                'original'    => $this->faker->randomElement([$this->faker->word, null]),
                'proposed'    => $this->faker->randomElement([$this->faker->word, null]),
                'type'    => $this->faker->randomElement(['add', 'remove', 'modify']),
            ];
            $changes[] = $change;
            $labels[$change['key']] = $this->faker->word;
        }

        $this->repo->createSet(
            $contract->id,
            $user->id,
            $userType,
            $changes,
            $labels,
            $reason
        );

        $changeOrderSet = ChangeOrder::whereContractId($contract->id)
            ->whereNull('parent_id')
            ->whereReason($reason)
            ->whereInitiatedByUser($user->id)
            ->whereInitiatedByParty($userType)
            ->first();

        $this->assertNotNull($changeOrderSet);

        foreach ($changes as $change) {
            $this->assertTrue(
                ChangeOrder::whereContractId($contract->id)
                    ->whereParentId($changeOrderSet->id)
                    ->whereInitiatedByUser($user->id)
                    ->whereInitiatedByParty($userType)
                    ->whereChangeKey($change['key'])
                    ->whereChangeDisplay($labels[$change['key']])
                    ->whereOriginalValue($change['original'])
                    ->whereProposedValue($change['proposed'])
                    ->whereChangeType($change['type'])
                    ->exists()
            );
        }
    }
}