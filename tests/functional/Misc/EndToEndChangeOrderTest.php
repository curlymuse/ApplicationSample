<?php

namespace Test\Functional\Misc;

use App\Models\Attachment;
use App\Models\ChangeOrder;
use App\Models\Contract;
use App\Models\ContractTerm;
use App\Models\ContractTermGroup;
use App\Models\Event;
use App\Models\Hotel;
use App\Models\Licensee;
use App\Models\PaymentMethod;
use App\Models\Proposal;
use App\Models\ProposalRequest;
use App\Models\ReservationMethod;
use App\Models\RoomSet;
use App\Transformers\Contract\ContractTransformer;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Tests\Traits\IncorporatesChangeOrderRequests;

class EndToEndChangeOrderTest extends TestCase
{
    use IncorporatesChangeOrderRequests;

    protected static $simpleFields = [
        'attrition_rate',
        'is_meeting_space_required',
        'is_food_and_beverage_required',
        'min_length_of_stay',
        'commission',
        'rebate',
        'additional_charge_per_adult',
        'tax_rate',
        'min_age_to_check_in',
        'additional_fees',
        'additional_fees_units',
        'deposit_policy',
        'cancellation_policy',
        'cancellation_policy_days',
        'cancellation_policy_file',
    ];

    protected static $jsonColumns = [
        'meeting_spaces',
        'questions',
        'food_and_beverage',
    ];

    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var \App\Models\Contract
     */
    protected $contract;

    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->actingAsLicensee();

        $this->contract = factory(Contract::class)->create([
            'proposal_id'   => factory(Proposal::class)->create([
                'proposal_request_id'   => factory(ProposalRequest::class)->create([
                    'event_id'  => factory(Event::class)->create([
                        'licensee_id'   => $this->licensee->id,
                    ])->id,
                ])->id,
            ])->id,
        ]);
        for ($i = 0; $i < $this->faker->numberBetween(1, 2); $i++) {
            $this->contract->roomSets()->save(
                factory(RoomSet::class)->create()
            );
        }
        for ($i = 0; $i < $this->faker->numberBetween(1, 2); $i++) {
            $this->contract->attachments()->save(
                factory(Attachment::class)->create()
            );
        }
        for ($i = 0; $i < $this->faker->numberBetween(1, 2); $i++) {
            $this->contract->reservationMethods()->save(
                factory(ReservationMethod::class)->create()
            );
        }
        for ($i = 0; $i < $this->faker->numberBetween(1, 2); $i++) {
            $this->contract->paymentMethods()->save(
                factory(PaymentMethod::class)->create()
            );
        }
        for ($i = 0; $i < $this->faker->numberBetween(2, 3); $i++) {
            $termGroup = factory(ContractTermGroup::class)->create([
                'contract_id'   => $this->contract->id,
            ]);
            factory(ContractTerm::class)->create([
                'contract_term_group_id' => $termGroup->id,
            ]);
        }

        $this->convertContractToInputData();
    }

    /**
     * This test submits the contract data exactly as is and asserts that no change order was created
     */
    public function test_flow_no_changes()
    {
        $data = [
            'reason'    => $this->faker->sentence,
            'changes'   => json_encode($this->inputData),
            'labels'    => json_encode('{example_field: "example label"}'),
            'add_attachments' => [
                'files' => [],
                'categories' => json_encode([]),
            ],
            'remove_attachments' => json_encode([]),
        ];
        $this->post(
            sprintf(
                'api/licensee/contracts/%d/change-orders',
                $this->contract->id
            ),
            $data
        );

        $this->assertNull($this->getJsonResponse()->change_order_id);
    }

    /**
     * This test makes the following changes to the contract data, submits it, then
     * asserts that all changes have been made to the contract after the change order has been accepted:
     *
     * 1. Modifies a simple meta-data column on the parent Contract object
     * 2. Modifies a single column of a single RoomSet object attached to the Contract
     * 3. Modifies the name of a TermGroup attached to the Contract
     * 4. Modifies the description attribute of a Term
     * 5. Modifies an inner key of a random element of a JSON-formatted column on the Contract
     * 6. Adds a RoomSet object
     * 7. Adds a Term to an existing TermGroup
     * 8. Adds an entire TermGroup, complete with an attached Term item
     * 9. Adds an entire item to a JSON-formatted column
     */
    public function test_flow_modifies_and_adds()
    {
        //  Change #1: Modify a simple column
        $simpleColumn = $this->faker->randomElement(self::$simpleFields);
        $this->inputData[$simpleColumn] = $this->faker->numberBetween(1, 1000);

        //  Change #2: Modify room set
        $this->inputData['rooms'][0]['rate'] = $this->faker->numberBetween(1, 1000);

        //  Change #3: Modify term group name
        $this->inputData['term_groups'][0]['name'] = $this->faker->word;

        //  Change #4: Modify term description / title
        $this->inputData['term_groups'][0]['terms'][0]['description'] = $this->faker->word;
        $this->inputData['term_groups'][0]['terms'][0]['title'] = $this->faker->word;

        //  Change #5: Modify JSON column
        $jsonColumn = $this->faker->randomElement(self::$jsonColumns);
        $jsonItem =& $this->inputData[$jsonColumn][0];
        $jsonKey = $this->faker->randomElement(
            array_keys(
                collect($jsonItem)
                    ->except(['id', 'index', 'group'])
                    ->toArray()
            )
        );
        $jsonItem[$jsonKey] = $this->faker->word;

        //  Change #6: Add a room set
        $newRoomSetData = [
            'rate'  => $this->faker->randomFloat(2, 1, 1000),
            'description'   => $this->faker->sentence,
            'name'  => $this->faker->word,
            'date'  => $this->faker->date('Y-m-d'),
            'rooms'  => $this->faker->numberBetween(1, 1000),
        ];
        $this->inputData['rooms'][] = $newRoomSetData;

        //  Change #7: Add a term to an existing term group
        $newTerm = [
            'description'  => $this->faker->sentence,
            'title'  => $this->faker->word,
        ];
        $this->inputData['term_groups'][0]['terms'][] = $newTerm;

        //  Change #8: Add a new term group with a new term
        $newTermGroup = [
            'name'      => $this->faker->word,
            'terms'     => [
                [
                    'description'   => $this->faker->sentence,
                    'title'         => $this->faker->word,
                ],
            ],
        ];
        $this->inputData['term_groups'][] = $newTermGroup;

        //  Change #9: JSON addition
        $additionJsonColumn = $this->faker->randomElement(self::$jsonColumns);
        $this->inputData[$additionJsonColumn][] = $this->getDummyArray();

        //  Change #10: Add new reservation method
        $reservationMethod = factory(ReservationMethod::class)->create();
        $this->inputData['reservation_methods'][] = $reservationMethod->id;

        //  Change #11: Add new payment method
        $paymentMethod = factory(PaymentMethod::class)->create();
        $this->inputData['payment_methods'][] = $paymentMethod->id;

        $contract = $this->submitAndConfirmChanges();

        //  Change #1: New simple field on contract
        $this->assertEquals($this->inputData[$simpleColumn], $contract->$simpleColumn);

        //  Change #2: Room set
        $this->assertEquals(
            $this->inputData['rooms'][0]['rate'],
            $contract->roomSets()->whereId($this->inputData['rooms'][0]['id'])->first()->rate
        );

        //  Change #3: Term group name
        $this->assertEquals(
            $this->inputData['term_groups'][0]['name'],
            $contract->termGroups()->whereId($this->inputData['term_groups'][0]['id'])->first()->name
        );

        //  Change #4: Term description / title
        $term = $contract->termGroups()
                ->whereId($this->inputData['term_groups'][0]['id'])
                ->first()
                ->terms()
                ->whereId($this->inputData['term_groups'][0]['terms'][0]['id'])
                ->first();
        $this->assertEquals(
            $this->inputData['term_groups'][0]['terms'][0]['description'],
            $term->description
        );
        $this->assertEquals(
            $this->inputData['term_groups'][0]['terms'][0]['title'],
            $term->title
        );

        //  Change #5: Json modify
        $foundJsonData = json_decode($contract->$jsonColumn);
        $this->assertEquals(
            $jsonItem[$jsonKey],
            $foundJsonData[0]->$jsonKey
        );

        //  Change #6: Add room set
        $this->assertTrue(
            $contract->roomSets()
                ->whereRate($newRoomSetData['rate'])
                ->whereDescription($newRoomSetData['description'])
                ->whereName($newRoomSetData['name'])
                ->whereReservationDate($newRoomSetData['date'] . ' 00:00:00')
                ->whereRoomsOffered($newRoomSetData['rooms'])
                ->exists()
        );

        //  Change #7: Add term to term group
        $this->assertTrue(
            $contract->termGroups()
                ->whereId($this->inputData['term_groups'][0]['id'])
                ->first()
                ->terms()
                ->whereDescription($newTerm['description'])
                ->exists()
        );

        //  Change #8: Add term group
        $this->assertTrue(
            $contract->termGroups()
                ->whereName($newTermGroup['name'])
                ->whereHas('terms', function($query) use ($newTermGroup) {
                    $query->whereDescription($newTermGroup['terms'][0]['description'])
                        ->whereTitle($newTermGroup['terms'][0]['title']);
                })->exists()
        );

        //  Change #9: Add JSON item
        $foundJsonData = json_decode($contract->$additionJsonColumn);
        $checkJsonItem = $this->inputData[$additionJsonColumn][count($this->inputData[$additionJsonColumn]) - 1];
        $checkJsonItem['index'] = count($this->inputData[$additionJsonColumn]) - 1;
        $this->assertEquals(
            $checkJsonItem,
            (array)$foundJsonData[count($foundJsonData) - 1]
        );

        //  Change #10: Add reservation method
        $this->assertContains($reservationMethod->id, $contract->reservationMethods()->pluck('reservation_methods.id'));

        //  Change #11: Add payment method
        $this->assertContains($paymentMethod->id, $contract->paymentMethods()->pluck('payment_methods.id'));
    }

    /**
     * This test performs all types of "remove" operations on the Contract and checks for correctly
     * modified data after the ChangeOrder is accepted:
     *
     * 1. Removes a RoomSet
     * 2. Removes an entire TermGroup
     * 3. Removes a single Term item from an existing TermGroup
     * 4. Removes a row from a JSON-formatted column
     */
    public function test_flow_removes()
    {
        //  Change #1: Remove a room
        $removeRoomSetId = $this->faker->randomElement(
            $this->contract->roomSets()->pluck('id')->toArray()
        );
        $this->inputData['rooms'] = collect($this->inputData['rooms'])
            ->reject(function($item) use ($removeRoomSetId) {
                return $item['id'] == $removeRoomSetId;
            })
            ->toArray();

        //  Change #2: Remove a term group
        $removeTermGroupId = $this->faker->randomElement(
            $this->contract->termGroups()->pluck('id')->toArray()
        );
        $this->inputData['term_groups'] = collect($this->inputData['term_groups'])
            ->reject(function($item) use ($removeTermGroupId) {
                return $item['id'] == $removeTermGroupId;
            })
            ->toArray();

        //  Change #3: Remove a term from a group
        $groupForTermRemoval = $this->contract->termGroups()->where('id', '!=', $removeTermGroupId)->first();
        $removeTermId = $this->faker->randomElement(
            $groupForTermRemoval->terms()->pluck('id')->toArray()
        );
        foreach ($this->inputData['term_groups'] as &$group) {
            if ($group['id'] != $groupForTermRemoval->id) {
                continue;
            }
            $group['terms'] = collect($group['terms'])
                ->reject(function($term) use ($removeTermId) {
                    return $term['id'] == $removeTermId;
                })
                ->toArray();
        }

        //  Change #4: Remove a JSON item
        $removeJsonColumn = $this->faker->randomElement(self::$jsonColumns);
        $jsonData =& $this->inputData[$removeJsonColumn];
        $removeJsonIndex = $this->faker->randomElement(array_keys($jsonData));
        $jsonData = collect($jsonData)->reject(function($item, $index) use ($removeJsonIndex) {
            return $index == $removeJsonIndex;
        })->toArray();

        //  Change #5: Remove a reservation method
        $removeReservationMethodId = $this->faker->randomElement(
            $this->inputData['reservation_methods']
        );
        $this->inputData['reservation_methods'] = collect($this->inputData['reservation_methods'])
            ->reject(function($id) use ($removeReservationMethodId) {
                return $id == $removeReservationMethodId;
            })->toArray();

        //  Change #6: Remove a payment method
        $removePaymentMethodId = $this->faker->randomElement(
            $this->inputData['payment_methods']
        );
        $this->inputData['payment_methods'] = collect($this->inputData['payment_methods'])
            ->reject(function($id) use ($removePaymentMethodId) {
                return $id == $removePaymentMethodId;
            })->toArray();

        $contract = $this->submitAndConfirmChanges();

        //  Change #1: Removed room set
        $this->assertFalse(
            $contract->roomSets()
                ->whereId($removeRoomSetId)
                ->exists()
        );

        //  Change #2: Remove term group
        $this->assertFalse(
            $contract->termGroups()
                ->whereId($removeTermGroupId)
                ->exists()
        );

        //  Change #3: Remove term from group
        $this->assertFalse(
            $contract->termGroups()
                ->whereId($groupForTermRemoval->id)
                ->first()
                ->terms()
                ->whereId($removeTermId)
                ->exists()
        );

        //  Change #4: Remove JSON item
        $this->assertEquals(
            $jsonData,
            json_decode($contract->$removeJsonColumn)
        );

        //  Change #5: Remove reservation method
        $this->assertNotContains($removeReservationMethodId, $contract->reservationMethods()->pluck('reservation_methods.id'));

        //  Change #6: Remove payment method
        $this->assertNotContains($removePaymentMethodId, $contract->paymentMethods()->pluck('payment_methods.id'));
    }

    /**
     * This tests for correct removal of Attachment
     */
    public function test_remove_attachments()
    {
        $removeAttachmentId = $this->faker->randomElement(
            $this->contract->attachments()->pluck('id')->toArray()
        );

        //  Create the change order
        $data = [
            'reason'    => $this->faker->sentence,
            'changes'   => json_encode($this->inputData),
            'labels'    => json_encode('{whatever: "whatevs"}'),
            'add_attachments'   => [
                'files' => [],
                'categories'    => json_encode([]),
            ],
            'remove_attachments'   => json_encode([
                $removeAttachmentId
            ]),
        ];
        $contract = $this->submitAndConfirmChanges($data);

        $this->assertFalse(
            $contract->attachments()
                ->whereId($removeAttachmentId)
                ->exists()
        );
    }

    /**
     * This test mocks the file upload function and tests for correct result
     */
    public function test_add_attachments()
    {
        //  For the purposes of this test, clear the existing attachments on the Contract
        $this->contract->attachments()->delete();

        //  Create a mock upload file and category
        $category = $this->faker->word;
        $file = new UploadedFile(
            app_path() . '/../resources/stubs/test-upload-file.txt',
            'test-upload-file.txt',
            'text/plain',
            null,
            null,
            $testing = true
        );

        //  Submit the data
        $data = [
            'reason'    => $this->faker->sentence,
            'changes'   => json_encode($this->inputData),
            'labels'    => json_encode('{whatever: "whatevs"}'),
            'add_attachments'   => [
                'files' => [],
                'categories'    => json_encode([
                    $category,
                ]),
            ],
            'remove_attachments'   => json_encode([])
        ];
        $this->call(
            'POST',
            sprintf(
                'api/licensee/contracts/%d/change-orders',
                $this->contract->id
            ),
            $data,
            [],
            [
                'add_attachments'   => [
                    'files' => [
                        $file
                    ]
                ]
            ]
        );

        //  Make sure a change order was created
        $changeOrderId = $this->getJsonResponse()->change_order_id;

        $this->assertNotNull($changeOrderId);

        //  Switch to Licensee
        $licensee = Licensee::find($this->contract->proposal->proposalRequest->event->licensee_id);
        $this->actingAsLicensee($licensee);

        //  Accept all elements of the change order
        $changeOrder = ChangeOrder::find($changeOrderId);
        $changes = [];
        foreach ($changeOrder->children as $item) {
            $changes[] = [
                'id'    => $item->id,
                'accepted' => 1
            ];
        }

        $this->assertCount(1, $changes);

        $this->post(
            sprintf(
                'api/licensee/contracts/%d/change-orders/%d/respond',
                $this->contract->id,
                $changeOrderId
            ),
            compact('changes')
        );

        $contract = Contract::find($this->contract->id);

        $attachment = $contract->attachments()->first();

        $this->assertNotNull($attachment);
        $this->assertEquals($category, $attachment->category);
    }

    private function submitAndConfirmChanges($data = null)
    {
        $data = ($data) ? $data : [
            'reason'    => $this->faker->sentence,
            'changes'   => json_encode($this->inputData),
            'labels'    => json_encode('{whatever: "whatevs"}'),
            'add_attachments'   => [
                'files' => [],
                'categories'    => json_encode([]),
            ],
            'remove_attachments'   => json_encode([]),
        ];
        $this->post(
            sprintf(
                'api/licensee/contracts/%d/change-orders',
                $this->contract->id
            ),
            $data
        );

        //  Make sure a change order was created
        $changeOrderId = $this->getJsonResponse()->change_order_id;

        $this->assertNotNull($changeOrderId);

        //  Switch to Licensee
        $licensee = Licensee::find($this->contract->proposal->proposalRequest->event->licensee_id);
        $this->actingAsLicensee($licensee);

        //  Accept all elements of the change order
        $changeOrder = ChangeOrder::find($changeOrderId);
        $changes = [];
        foreach ($changeOrder->children as $item) {
            $changes[] = [
                'id'    => $item->id,
                'accepted' => 1
            ];
        }

        $this->post(
            sprintf(
                'api/licensee/contracts/%d/change-orders/%d/respond',
                $this->contract->id,
                $changeOrderId
            ),
            compact('changes')
        );

        return Contract::find($this->contract->id);
    }
}
