<?php

namespace Tests\Integration\Repositories;

use App\Models\ContractTermGroup;
use App\Repositories\Contracts\ContractTermRepositoryInterface;
use Tests\TestCase;

/**
 * Class ContractTermRepositoryTest
 *
 * @coversBaseClass \App\Repositories\ContractTermRepository
 */
class ContractTermRepositoryTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var \App\Repositories\Contracts\ContractTermRepositoryInterface
     */
    private $repo;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->repo = app(ContractTermRepositoryInterface::class);
    }

    /**
     * @covers ::storeForGroup
     */
    public function test_storeForGroup()
    {
        $group = factory(ContractTermGroup::class)->create();
        $description = $this->faker->paragraph;
        $title = $this->faker->word;

        $this->repo->storeForGroup($group->id, $title, $description);

        $this->seeInDatabase(
            'contract_terms',
            [
                'contract_term_group_id'    => $group->id,
                'description'               => $description,
                'title'               => $title,
            ]
        );
    }
}