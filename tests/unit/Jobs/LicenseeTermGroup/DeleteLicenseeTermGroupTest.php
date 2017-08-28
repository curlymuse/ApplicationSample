<?php

namespace Tests\Unit\Jobs\LicenseeTermGroup;

use App\Events\Licensee\TermGroups\LicenseeTermGroupWasDeleted;
use App\Jobs\LicenseeTermGroup\DeleteLicenseeTermGroup;
use App\Repositories\Contracts\LicenseeTermGroupRepositoryInterface;
use Tests\TestCase;

/**
 * Class DeleteLicenseeTermGroupTest
 *
 * @coversBaseClass App\Jobs\LicenseeTermGroup\DeleteLicenseeTermGroup
 */
class DeleteLicenseeTermGroupTest extends TestCase
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

        $this->groupRepo->shouldReceive('delete')
            ->once()
            ->with(
                $groupId
            );

        $this->expectsEvents(LicenseeTermGroupWasDeleted::class);

        dispatch(
            new DeleteLicenseeTermGroup(
                $groupId
            )
        );
    }
}