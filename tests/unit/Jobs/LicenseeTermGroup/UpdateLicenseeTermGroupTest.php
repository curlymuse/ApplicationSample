<?php

namespace Tests\Unit\Jobs\LicenseeTermGroup;

use App\Events\Licensee\TermGroups\LicenseeTermGroupWasUpdated;
use App\Jobs\LicenseeTermGroup\UpdateLicenseeTermGroup;
use App\Repositories\Contracts\LicenseeTermGroupRepositoryInterface;
use Tests\TestCase;

/**
 * Class UpdateLicenseeTermGroupTest
 *
 * @coversBaseClass App\Jobs\LicenseeTermGroup\UpdateLicenseeTermGroup
 */
class UpdateLicenseeTermGroupTest extends TestCase
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
        $groupId = $this->faker->numberBetween(1, 1000);
        $name = $this->faker->word;

        $this->groupRepo->shouldReceive('update')
            ->once()
            ->with(
                $groupId,
                compact('name')
            );

        $this->expectsEvents(LicenseeTermGroupWasUpdated::class);

        dispatch(
            new UpdateLicenseeTermGroup(
                $groupId,
                $name
            )
        );
    }
}