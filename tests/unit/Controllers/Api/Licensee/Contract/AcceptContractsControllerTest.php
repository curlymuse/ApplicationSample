<?php

namespace Tests\Unit\Controllers\Api\Licensee\Contract;

use App\Http\Requests\Contract\SignContractRequest;
use App\Jobs\Contract\AcceptContract;
use App\Models\Contract;
use App\Models\Event;
use App\Models\Proposal;
use App\Models\ProposalRequest;
use Tests\TestCase;

/**
 * Class AcceptContractsControllerTest
 *
 * @coversBaseClass \App\Http\Controllers\Api\Licensee\Contract\AcceptContractsController
 */
class AcceptContractsControllerTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var \App\Models\Contract
     */
    protected $contract;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->actingAsLicensee();

        $this->contract = factory(Contract::class)->create([
            'proposal_id'   => factory(Proposal::class)->create([
                'proposal_request_id'       => factory(ProposalRequest::class)->create([
                    'event_id'      => factory(Event::class)->create([
                        'licensee_id'       => $this->licensee->id,
                    ])->id
                ])->id
            ])->id
        ]);
    }

    /**
     * @covers ::store
     */
    public function test_store()
    {
        $data = [
            'signature' => $this->faker->word,
        ];

        $this->expectsFormRequest(SignContractRequest::class, $data);
        $this->expectsJobs(AcceptContract::class);

        $this->action('POST', 'Api\Licensee\Contract\AcceptContractsController@store', $this->contract->id);
        $this->assertResponseOk();
    }
}