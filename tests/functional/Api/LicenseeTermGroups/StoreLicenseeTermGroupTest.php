<?php

namespace Tests\Functional\Api\LicenseeTermGroups;

use Tests\TestCase;

class StoreLicenseeTermGroupTest extends TestCase
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
        $data = [
            'name'      => $this->faker->name,
        ];

        $this->post('api/licensee/term-groups', $data);

        $response = $this->getJsonResponse();

        $this->seeInDatabase(
            'licensee_term_groups',
            [
                'id'        => $response->group_id,
                'licensee_id'    => $this->licensee->id,
                'name'      => $data['name'],
            ]
        );
    }
}
