<?php

namespace Tests\Integration\Repositories;

use App\Models\LicenseeTermGroup;
use App\Repositories\Contracts\LicenseeTermRepositoryInterface;
use Tests\TestCase;

/**
 * Class LicenseeTermRepositoryTest
 *
 * @coversBaseClass \App\Repositories\LicenseeTermRepository
 */
class LicenseeTermRepositoryTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var \App\Repositories\Contracts\LicenseeTermRepositoryInterface
     */
    private $repo;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->repo = app(LicenseeTermRepositoryInterface::class);
    }

    /**
     * @covers ::storeForTermGroup
     */
    public function test_storeForTermGroup()
    {
        $description = $this->faker->paragraph;
        $title = $this->faker->word;
        $group = factory(LicenseeTermGroup::class)->create();

        $this->repo->storeForTermGroup($group->id, $title, $description);

        $this->assertTrue(
            $group->terms()
                ->whereDescription($description)
                ->whereTitle($title)
                ->exists()
        );
    }
}