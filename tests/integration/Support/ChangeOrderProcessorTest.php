<?php

namespace Tests\Integration\Support;

use App\Models\Attachment;
use App\Models\ChangeOrder;
use App\Models\Contract;
use App\Models\ContractTerm;
use App\Models\ContractTermGroup;
use App\Models\PaymentMethod;
use App\Models\ReservationMethod;
use App\Models\RoomSet;
use App\Support\ChangeOrderProcessor;
use Tests\TestCase;

class ChangeOrderProcessorTest extends TestCase
{
    /**
     * @var array
     */
    private static $metaFields = [
        'min_length_of_stay',
        'start_date',
        'end_date',
        'check_in_date',
        'check_out_date',
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

    /**
     * @var array
     */
    protected static $jsonColumns = [
        'questions',
        'meeting_spaces',
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

    /**
     * @var \App\Support\ChangeOrderProcessor
     */
    protected $processor;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->processor = app(ChangeOrderProcessor::class);

        //  Create a complete contract
        $this->contract = factory(Contract::class)->create();
        for ($i = 0; $i < $this->faker->numberBetween(1, 2); $i++) {
            factory(RoomSet::class)->create([
                'contract_id' => $this->contract->id,
            ]);
        }
        for ($i = 0; $i < $this->faker->numberBetween(1, 2); $i++) {
            $termGroup = factory(ContractTermGroup::class)->create([
                'contract_id'   => $this->contract->id,
            ]);
            factory(ContractTerm::class)->create([
                'contract_term_group_id' => $termGroup->id,
            ]);
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
    }

    public function test_modify_meta_field()
    {
        $changeKey = $this->faker->randomElement(self::$metaFields);

        //  Use a number, so it will store properly regardless of the data type
        $isDate = in_array($changeKey, [
            'start_date',
            'end_date',
            'check_in_date',
            'check_out_date',
        ]);
        $changeTo = ($isDate) ? $this->faker->date : $this->faker->numberBetween(1, 1000);

        $this->processChangeOrder(
            'modify',
            $changeKey,
            $changeTo
        );

        $this->seeInDatabase(
            'contracts',
            [
                'id'    => $this->contract->id,
                $changeKey  => $changeTo,
            ]
        );
    }

    public function test_modify_json_item()
    {
        $column = $this->faker->randomElement(self::$jsonColumns);

        //  Store some JSON data
        $jsonData = [];
        $keys = [
            $this->faker->word,
            $this->faker->word,
        ];
        for ($i = 0; $i < $this->faker->numberBetween(1, 2); $i++) {
            $jsonData[] = [
                $keys[0]    => $this->faker->word,
                $keys[1]    => $this->faker->word,
                'index'     => $i,
            ];
        }
        $this->contract->update([
            $column => json_encode($jsonData),
        ]);

        $changeIndex = $this->faker->numberBetween(0, count($jsonData) - 1);
        $changeKey = $this->faker->randomElement($keys);
        $changeTo = $this->faker->word;

        $this->processChangeOrder(
            'modify',
            sprintf(
                '%s.index:%d.%s',
                $column,
                $changeIndex,
                $changeKey
            ),
            $changeTo
        );

        $contract = Contract::find($this->contract->id);
        $foundData = json_decode($contract->$column);

        $this->assertEquals(
            $changeTo,
            collect($foundData)
                ->where('index', $changeIndex)
                ->first()
                ->$changeKey
        );
    }

    public function test_modify_room_set()
    {
        $columnTranslator = [
            'rate'  => 'rate',
            'name'  => 'name',
            'description'   => 'description',
            'rooms' => 'rooms_offered',
            'date'  => 'reservation_date',
        ];

        $changeId = $this->faker->randomElement(
            $this->contract->roomSets()->pluck('id')->toArray()
        );
        $changeKey = $this->faker->randomElement(array_keys($columnTranslator));
        $changeTo = $this->faker->numberBetween(1, 1000);

        $this->processChangeOrder(
            'modify',
            sprintf(
                'rooms.id:%d.%s',
                $changeId,
                $changeKey
            ),
            $changeTo
        );

        $lookupColumn = $columnTranslator[$changeKey];
        $this->seeInDatabase(
            'room_sets',
            [
                'id'            => $changeId,
                'contract_id'   => $this->contract->id,
                $lookupColumn   => $changeTo,
            ]
        );
    }

    public function test_modify_term_group_name()
    {
        $groupId = $this->faker->randomElement(
            $this->contract->termGroups()->pluck('id')->toArray()
        );
        $changeTo = $this->faker->word;

        $this->processChangeOrder(
            'modify',
            sprintf(
                'term_groups.id:%d.name',
                $groupId
            ),
            json_encode([
                'name'  => $changeTo,
            ])
        );

        $this->seeInDatabase(
            'contract_term_groups',
            [
                'id'    => $groupId,
                'name'  => $changeTo,
            ]
        );
    }

    public function test_modify_term_description()
    {
        $groupId = $this->faker->randomElement(
            $this->contract->termGroups()->pluck('id')->toArray()
        );
        $termId = $this->faker->randomElement(
            $this->contract->termGroups()->whereId($groupId)->first()->terms()->pluck('id')->toArray()
        );
        $changeTo = $this->faker->paragraph;

        $this->processChangeOrder(
            'modify',
            sprintf(
                'term_groups.id:%d.terms.id:%d.description',
                $groupId,
                $termId
            ),
            json_encode([
                'description'  => $changeTo,
            ])
        );

        $this->seeInDatabase(
            'contract_terms',
            [
                'id'    => $termId,
                'contract_term_group_id'    => $groupId,
                'description'  => $changeTo,
            ]
        );
    }

    public function test_modify_term_title()
    {
        $groupId = $this->faker->randomElement(
            $this->contract->termGroups()->pluck('id')->toArray()
        );
        $termId = $this->faker->randomElement(
            $this->contract->termGroups()->whereId($groupId)->first()->terms()->pluck('id')->toArray()
        );
        $changeTo = $this->faker->word;

        $this->processChangeOrder(
            'modify',
            sprintf(
                'term_groups.id:%d.terms.id:%d.title',
                $groupId,
                $termId
            ),
            json_encode([
                'title'  => $changeTo,
            ])
        );

        $this->seeInDatabase(
            'contract_terms',
            [
                'id'    => $termId,
                'contract_term_group_id'    => $groupId,
                'title'  => $changeTo,
            ]
        );
    }

    public function test_remove_json_item()
    {
        $column = $this->faker->randomElement(self::$jsonColumns);

        //  Replace what is in the contract, so we have full control over all variables
        $baseData = [];
        for ($i = 0; $i < $this->faker->numberBetween(2, 3); $i++) {
            $baseData[] = collect($this->getDummyArray())->merge([
                'index' => $i + 1,
            ])->toArray();
        }
        $this->contract->update([
            $column     => json_encode($baseData)
        ]);

        //  In our Change Order, remove the last element
        $removeIndex = $this->faker->numberBetween(1, count($baseData));
        $this->processChangeOrder(
            'remove',
            sprintf(
                '%s.index:%d',
                $column,
                $removeIndex
            )
        );

        //  We expect to have what we started with, minus one element
        $expectedData = array_values(collect($baseData)->reject(function($item) use ($removeIndex) {
            return $item['index'] == $removeIndex;
        })->toArray());

        $this->seeInDatabase(
            'contracts',
            [
                $column => json_encode($expectedData),
            ]
        );
    }

    public function test_remove_room_set()
    {
        $roomSetId = $this->faker->randomElement(
            $this->contract->roomSets()->pluck('id')->toArray()
        );

        $this->processChangeOrder(
            'remove',
            sprintf(
                'rooms.id:%d',
                $roomSetId
            )
        );

        $this->dontSeeInDatabase(
            'room_sets',
            [
                'id'    => $roomSetId,
            ]
        );
    }

    public function test_remove_reservation_method()
    {
        $methodId = $this->faker->randomElement(
            $this->contract->reservationMethods()->pluck('reservation_methods.id')->toArray()
        );

        $this->processChangeOrder(
            'remove',
            sprintf(
                'reservation_methods.id:%d',
                $methodId
            )
        );

        $this->dontSeeInDatabase(
            'contract_reservation_method',
            [
                'contract_id'    => $this->contract->id,
                'reservation_method_id'    => $methodId,
            ]
        );
    }

    public function test_remove_payment_method()
    {
        $methodId = $this->faker->randomElement(
            $this->contract->paymentMethods()->pluck('payment_methods.id')->toArray()
        );

        $this->processChangeOrder(
            'remove',
            sprintf(
                'payment_methods.id:%d',
                $methodId
            )
        );

        $this->dontSeeInDatabase(
            'contract_payment_method',
            [
                'contract_id'    => $this->contract->id,
                'payment_method_id'    => $methodId,
            ]
        );
    }


    public function test_remove_term_group()
    {
        $groupId = $this->faker->randomElement(
            $this->contract->termGroups()->pluck('id')->toArray()
        );

        $this->processChangeOrder(
            'remove',
            sprintf(
                'term_groups.id:%d',
                $groupId
            )
        );

        $this->dontSeeInDatabase(
            'contract_term_groups',
            [
                'id'    => $groupId,
            ]
        );

        $this->dontSeeInDatabase(
            'contract_terms',
            [
                'contract_term_group_id'    => $groupId,
            ]
        );
    }

    public function test_remove_single_term()
    {
        $groupId = $this->faker->randomElement(
            $this->contract->termGroups()->pluck('id')->toArray()
        );
        $termId = $this->faker->randomElement(
            $this->contract->termGroups()->whereId($groupId)->first()->terms()->pluck('id')->toArray()
        );

        $this->processChangeOrder(
            'remove',
            sprintf(
                'term_groups.id:%d.terms.id:%d',
                $groupId,
                $termId
            )
        );

        $this->dontSeeInDatabase(
            'contract_terms',
            [
                'id'    => $termId,
            ]
        );
    }

    public function test_add_attachment()
    {
        $attachment = factory(Attachment::class)->create([
            'attachable_type'   => Contract::class,
            'attachable_id'     => $this->contract->id,
        ]);

        $changeOrder = factory(ChangeOrder::class)->create([
            'contract_id'   => $this->contract->id,
            'parent_id' => factory(ChangeOrder::class)->create()->id,
            'change_type' => 'add',
            'change_key'    => 'attachments',
            'proposed_value'    => $attachment->id,
        ]);

        $attachment->update([
            'attachable_type'   => ChangeOrder::class,
            'attachable_id'     => $changeOrder->id,
        ]);

        $this->processor->process($changeOrder->id);

        $this->seeInDatabase(
            'attachments',
            [
                'id'    => $attachment->id,
                'attachable_type'   => Contract::class,
                'attachable_id'     => $this->contract->id,
            ]
        );
    }

    public function test_remove_attachment()
    {
        $attachment = factory(Attachment::class)->create([
            'attachable_type'   => Contract::class,
            'attachable_id'     => $this->contract->id,
        ]);

        $this->processChangeOrder(
            'remove',
            sprintf(
                'attachments.id:%d',
                $attachment->id
            )
        );

        $this->assertFalse(
            Attachment::whereId($attachment->id)
                ->exists()
        );
    }

    public function test_add_json_item()
    {
        $dummyData = $this->getDummyArray();
        $column = $this->faker->randomElement(self::$jsonColumns);


        $this->processChangeOrder(
            'add',
            $column,
            json_encode($dummyData)
        );

        $contract = Contract::find($this->contract->id);

        $foundData = json_decode($contract->$column);

        $lastItem = collect(
            collect($foundData)->last()
        )->toArray();

        $expectedData = collect($dummyData)->merge([
            'index' => count(json_decode($this->contract->$column)),
        ])->toArray();
        $this->assertEquals($expectedData, $lastItem);
    }

    public function test_add_room_set()
    {
        $data = [
            'rate'  => $this->faker->randomFloat(2, 1, 100),
            'description'   => $this->faker->sentence,
            'rooms' => $this->faker->numberBetween(1, 5),
            'date'  => $this->faker->date,
            'name'  => $this->faker->word,
        ];

        $this->processChangeOrder(
            'add',
            'rooms',
            json_encode($data)
        );

        $this->seeInDatabase(
            'room_sets',
            [
                'contract_id'   => $this->contract->id,
                'rate'  => $data['rate'],
                'description'  => $data['description'],
                'name'  => $data['name'],
                'reservation_date'  => $data['date'] . ' 00:00:00',
                'rooms_offered'  => $data['rooms'],
            ]
        );
    }

    public function test_add_term()
    {
        $groupId = $this->faker->randomElement(
            $this->contract->termGroups()->pluck('id')->toArray()
        );

        $data = [
            'description'   => $this->faker->sentence,
            'title'   => $this->faker->word,
        ];

        $this->processChangeOrder(
            'add',
            sprintf(
                'term_groups.id:%d.terms',
                $groupId
            ),
            json_encode($data)
        );

        $this->seeInDatabase(
            'contract_terms',
            [
                'contract_term_group_id'   => $groupId,
                'description'   => $data['description'],
                'title'   => $data['title'],
            ]
        );
    }

    public function test_add_term_group()
    {
        $data = [
            'name'  => $this->faker->word,
            'terms' => [],
        ];
        for ($i = 0; $i < $this->faker->numberBetween(1, 2); $i++) {
            $data['terms'][] = [
                'description'   => $this->faker->paragraph,
                'title'   => $this->faker->word,
            ];
        }

        $this->processChangeOrder(
            'add',
            'term_groups',
            json_encode($data)
        );

        $group = ContractTermGroup::whereContractId($this->contract->id)
            ->whereName($data['name'])
            ->first();

        $this->assertNotNull($group);

        foreach ($data['terms'] as $term) {
            $this->seeInDatabase(
                'contract_terms',
                [
                    'contract_term_group_id'    => $group->id,
                    'description'   => $term['description'],
                    'title'   => $term['title'],
                ]
            );
        }
    }

    public function test_add_reservation_method()
    {
        $method = factory(ReservationMethod::class)->create();

        $this->processChangeOrder(
            'add',
            'reservation_methods',
            $method->id
        );

        $this->seeInDatabase(
            'contract_reservation_method',
            [
                'contract_id'   => $this->contract->id,
                'reservation_method_id' => $method->id,
            ]
        );
    }

    public function test_add_payment_method()
    {
        $method = factory(PaymentMethod::class)->create();

        $this->processChangeOrder(
            'add',
            'payment_methods',
            $method->id
        );

        $this->seeInDatabase(
            'contract_payment_method',
            [
                'contract_id'   => $this->contract->id,
                'payment_method_id' => $method->id,
            ]
        );
    }

    /**
     * Store a ChangeOrder for the test Contract
     *
     * @param string $type
     * @param string $key
     * @param string|null $changeTo
     *
     * @return ChangeOrder
     */
    private function processChangeOrder($type, $key, $changeTo = null)
    {
        $changeOrder = factory(ChangeOrder::class)->create([
            'contract_id'   => $this->contract->id,
            'parent_id' => factory(ChangeOrder::class)->create()->id,
            'change_type' => $type,
            'change_key'    => $key,
            'proposed_value'    => $changeTo,
        ]);

        $this->processor->process($changeOrder->id);

        return $changeOrder;
    }
}
