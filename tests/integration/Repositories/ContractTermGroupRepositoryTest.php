<?php

namespace Tests\Integration\Repositories;

use App\Models\Contract;
use App\Models\ContractTerm;
use App\Models\ContractTermGroup;
use App\Repositories\Contracts\ContractTermGroupRepositoryInterface;
use Tests\TestCase;

/**
 * Class ContractTermGroupRepositoryTest
 *
 * @coversBaseClass \App\Repositories\ContractTermGroupRepository
 */
class ContractTermGroupRepositoryTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var \App\Repositories\Contracts\ContractTermGroupRepositoryInterface
     */
    private $repo;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->repo = app(ContractTermGroupRepositoryInterface::class);
    }

    /**
     * @covers ::allForContract
     */
    public function test_allForContract()
    {
        $includeGroup = factory(ContractTermGroup::class)->create();
        $excludeGroup = factory(ContractTermGroup::class)->create();

        $groups = $this->repo->allForContract($includeGroup->contract_id);

        $this->assertContains($includeGroup->id, $groups->pluck('id'));
        $this->assertNotContains($excludeGroup->id, $groups->pluck('id'));
    }

    /**
     * @covers ::storeForContract
     */
    public function test_storeForContract()
    {
        $contract = factory(Contract::class)->create();
        $name = $this->faker->word;

        $this->repo->storeForContract($contract->id, $name);

        $this->seeInDatabase(
            'contract_term_groups',
            [
                'contract_id'   => $contract->id,
                'name'          => $name,
            ]
        );
    }

    /**
     * @covers ::removeGroupAndTerms
     */
    public function test_removeGroupAndTerms()
    {
        $group = factory(ContractTermGroup::class)->create();
        for ($i = 0; $i < $this->faker->numberBetween(1, 2); $i++) {
            factory(ContractTerm::class)->create([
                'contract_term_group_id'     => $group->id,
            ]);
        }

        $this->repo->removeGroupAndTerms($group->id);

        $this->dontSeeInDatabase(
            'contract_term_groups',
            [
                'id'    => $group->id,
            ]
        );

        $this->dontSeeInDatabase(
            'contract_terms',
            [
                'contract_term_group_id'    => $group->id,
            ]
        );
    }
}