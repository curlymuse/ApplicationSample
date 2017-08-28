<?php

namespace Tests\Unit\Jobs\LicenseeTermGroup;

use App\Events\Licensee\TermGroups\LicenseeTermGroupWasCreated;
use App\Jobs\LicenseeTermGroup\CreateLicenseeTermGroup;
use App\Models\LicenseeTermGroup;
use App\Repositories\Contracts\LicenseeTermGroupRepositoryInterface;
use Tests\TestCase;

/**
 * Class CreateLicenseeTermGroupTest
 *
 * @coversBaseClass App\Jobs\LicenseeTermGroup\CreateLicenseeTermGroup
 */
class CreateLicenseeTermGroupTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var \App\Repositories\Contracts\LicenseeTermGroupRepositoryInterface
     */
    private $groupRepo;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->groupRepo = $this->expectsRepository(LicenseeTermGroupRepositoryInterface::class);
    }

    public function test_handle()
    {
        $name = $this->faker->word;
        $licenseeId = $this->faker->numberBetween(1, 1000);

        $group = factory(LicenseeTermGroup::class)->create();
        $this->groupRepo->shouldReceive('storeForLicensee')
            ->once()
            ->with(
                $licenseeId,
                $name
            )
            ->andReturn($group);

        $this->expectsEvents(LicenseeTermGroupWasCreated::class);

        dispatch(
            new CreateLicenseeTermGroup(
                $licenseeId,
                $name
            )
        );
    }
}