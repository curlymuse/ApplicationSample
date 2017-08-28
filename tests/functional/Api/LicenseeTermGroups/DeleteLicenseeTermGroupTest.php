<?php

namespace Tests\Functional\Api\LicenseeTermGroups;

use App\Models\LicenseeTermGroup;
use Tests\TestCase;

class DeleteLicenseeTermGroupTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->actingAsLicensee();
    }

    public function test_endpoint()
    {
        $group = factory(LicenseeTermGroup::class)->create([
            'licensee_id'   => $this->licensee->id,
        ]);

        $this->delete(
            sprintf(
                '/api/licensee/term-groups/%d',
                $group->id
            )
        );

        $this->dontSeeInDatabase(
            'licensee_term_groups',
            [
                'id'        => $group->id,
                'licensee_id'    => $this->licensee->id,
                'name'      => $group->name,
            ]
        );
    }
}
