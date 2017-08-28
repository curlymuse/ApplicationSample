<?php

namespace Tests\Unit\Controllers\Api\Licensee\Contract;

use App\Http\Requests\ChangeOrder\CreateChangeOrderRequest;
use App\Jobs\Attachment\ProcessTemporaryContractAttachments;
use App\Jobs\ChangeOrder\CreateChangeOrderSet;
use App\Models\Contract;
use App\Models\Event;
use App\Models\Licensee;
use App\Models\Proposal;
use App\Models\ProposalRequest;
use App\Repositories\Contracts\ChangeOrderRepositoryInterface;
use App\Transformers\ChangeOrder\ChangeOrderSetTransformer;
use App\Transformers\ChangeOrder\ChangeOrderTransformer;
use Tests\TestCase;
use Tests\Unit\Jobs\ChangeOrder\CreateChangeOrderSetTest;

/**
 * Class ChangeOrdersControllerTest
 *
 * @coversBaseClass \App\Http\Controllers\Api\Licensee\Contract\ChangeOrdersController
 */
class ChangeOrdersControllerTest extends TestCase
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
     * @var \App\Repositories\Contracts\ChangeOrderRepositoryInterface
     */
    private $changeOrderRepo;

    /**
     * @var \App\Transformers\ChangeOrder\ChangeOrderTransformer
     */
    private $transformer;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->actingAsLicensee();

        $this->changeOrderRepo = $this->expectsRepository(ChangeOrderRepositoryInterface::class);
        $this->transformer = $this->mock(ChangeOrderSetTransformer::class);

        $this->contract = factory(Contract::class)->create([
            'proposal_id' => factory(Proposal::class)->create([
                'proposal_request_id' => factory(ProposalRequest::class)->create([
                    'event_id' => factory(Event::class)->create([
                        'licensee_id' => $this->licensee->id,
                    ])->id,
                ])->id,
            ])->id,
        ]);
    }

    /**
     * @covers ::index
     */
    public function test_index()
    {
        $dummyCollection = $this->getDummyCollection();

        $this->changeOrderRepo->shouldReceive('allForContract')
            ->once()
            ->with($this->contract->id)
            ->andReturn($dummyCollection);

        $this->transformer->shouldReceive('transformCollection')
            ->once()
            ->with($dummyCollection)
            ->andReturn($dummyCollection);

        $this->action('GET', 'Api\Licensee\Contract\ChangeOrdersController@index', $this->contract->id);
        $this->assertResponseOk();
    }

    /**
     * @covers ::store
     */
    public function test_store()
    {
        $data = [
            'reason'    => $this->faker->sentence,
            'changes' => $this->getDummyArray(),
            'labels'    => $this->getDummyArray(),
            'remove_attachments'    => $this->getDummyArray(),
            'add_attachments'    => [
                'files' => $this->getDummyArray(),
                'categories' => $this->getDummyArray(),
            ],
        ];

        $this->expectsFormRequest(CreateChangeOrderRequest::class, $data);

        $attachmentIds = $this->getDummyArray();
        $changeOrderId = $this->faker->numberBetween(1, 1000);
        $this->expectsJobsWithReturns(
            [
                ProcessTemporaryContractAttachments::class => $attachmentIds,
                CreateChangeOrderSet::class => $changeOrderId,
                LinkAttachmentsToChangeOrders::class => null,
            ]
        );

        $this->action('POST', 'Api\Licensee\Contract\ChangeOrdersController@store', $this->contract->id);
        $this->assertResponseOk();
    }
}
