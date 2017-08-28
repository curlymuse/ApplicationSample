<?php

namespace Tests\Functional\Api\LicenseeTerms;

use App\Models\LicenseeTerm;
use App\Models\LicenseeTermGroup;
use Tests\TestCase;

class DeleteLicenseeTermTest extends TestCase
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
            'licensee_id'       => $this->licensee->id,
        ]);
        $term = factory(LicenseeTerm::class)->create([
            'licensee_term_group_id'       => $group->id,
        ]);

        $this->delete(
            sprintf(
                '/api/licensee/term-groups/%d/terms/%d',
                $group->id,
                $term->id
            )
        );

        $this->dontSeeInDatabase(
            'licensee_terms',
            [
                'id'        => $term->id,
                'licensee_term_group_id'    => $group->id,
                'description'      => $term->description,
            ]
        );
    }
}
