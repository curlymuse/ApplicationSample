<?php

namespace Tests\Unit\Controllers\Api\Licensee\Contract;

use App\Http\Requests\ChangeOrder\ChangeOrderResponseRequest;
use App\Jobs\ChangeOrder\ProcessChangeOrderResponses;
use App\Models\ChangeOrder;
use App\Models\Contract;
use App\Models\Event;
use App\Models\Licensee;
use App\Models\Proposal;
use App\Models\ProposalRequest;
use Tests\TestCase;

/**
 * Class ChangeOrderResponsesControllerTest
 *
 * @coversBaseClass \App\Http\Controllers\Api\Licensee\Contract\ChangeOrderResponsesController
 */
class ChangeOrderResponsesControllerTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var \App\Models\ChangeOrder
     */
    protected $changeOrder;

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
            'proposal_id'       => factory(Proposal::class)->create([
                'proposal_request_id'   => factory(ProposalRequest::class)->create([
                    'event_id'  => factory(Event::class)->create([
                        'licensee_id'   => $this->licensee->id,
                    ])->id,
                ])->id,
            ])->id,
        ]);

        $this->changeOrder = factory(ChangeOrder::class)->create([
            'contract_id'   => $this->contract->id,
            'initiated_by_party' => 'licensee',
        ]);
    }

    /**
     * @covers ::store
     */
    public function test_store()
    {
        $data = [
            'changes'   => $this->getDummyArray(),
        ];

        $this->expectsFormRequest(ChangeOrderResponseRequest::class, $data);
        $this->expectsJobs(ProcessChangeOrderResponses::class);

        $this->action(
            'POST',
            'Api\Licensee\Contract\ChangeOrderResponsesController@store',
            [
                $this->contract->id,
                $this->changeOrder->id,
            ],
            $data
        );
        $this->assertResponseOk();
    }
}