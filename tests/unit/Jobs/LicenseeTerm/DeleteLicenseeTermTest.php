<?php

namespace Tests\Unit\Jobs\LicenseeTerm;

use App\Events\Licensee\Terms\LicenseeTermWasDeleted;
use App\Jobs\LicenseeTerm\DeleteLicenseeTerm;
use App\Repositories\Contracts\LicenseeTermRepositoryInterface;
use Tests\TestCase;

/**
 * Class DeleteLicenseeTermTest
 *
 * @coversBaseClass App\Jobs\LicenseeTerm\DeleteLicenseeTerm
 */
class DeleteLicenseeTermTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var \App\Repositories\Contracts\LicenseeTermRepositoryInterface
     */
    private $termRepo;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->termRepo = $this->expectsRepository(LicenseeTermRepositoryInterface::class);
    }

    public function test_handle()
    {
        $termId = $this->faker->numberBetween(1, 1000);

        $this->termRepo->shouldReceive('delete')
            ->once()
            ->with($termId);

        $this->expectsEvents(LicenseeTermWasDeleted::class);

        dispatch(
            new DeleteLicenseeTerm(
                $termId
            )
        );
    }
}