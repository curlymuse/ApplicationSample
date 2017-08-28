<?php

namespace Tests\Integration\Support;

use App\Models\Contract;
use App\Models\ContractTerm;
use App\Models\ContractTermGroup;
use App\Models\PaymentMethod;
use App\Models\ReservationMethod;
use App\Models\RoomSet;
use App\Support\ChangeOrderParser;
use App\Transformers\Contract\ContractTransformer;
use Tests\Traits\IncorporatesChangeOrderRequests;
use Tests\TestCase;

class ChangeOrderParserTest extends TestCase
{
    use IncorporatesChangeOrderRequests;

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
     * @var array
     */
    protected $inputData = [];

    /**
     * @var array
     */
    protected $addAttachments = [];

    /**
     * @var array
     */
    protected $removeAttachments = [];

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

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

        //  Create an input set that exactly the matches the contract
        $this->convertContractToInputData();
    }

    public function test_no_changes()
    {
        $this->assertCount(0, $this->getChanges());
    }

    public function test_add_attachments()
    {
        for ($i = 0; $i < $this->faker->numberBetween(1, 2); $i++) {
            $this->addAttachments[] = $this->faker->numberBetween(1, 1000);
        }

        $changes = $this->getChanges();

        $this->assertCount(count($this->addAttachments), $changes);

        foreach ($this->addAttachments as $id) {
            $this->assertContains(
                [
                    'type'  => 'add',
                    'key'   => 'attachments',
                    'proposed'  => $id,
                ],
                $changes
            );
        }
    }

    public function test_remove_attachments()
    {
        for ($i = 0; $i < $this->faker->numberBetween(1, 2); $i++) {
            $this->removeAttachments[] = $this->faker->numberBetween(1, 1000);
        }

        $changes = $this->getChanges();

        $this->assertCount(count($this->removeAttachments), $changes);

        foreach ($this->removeAttachments as $id) {
            $this->assertContains(
                [
                    'type'  => 'remove',
                    'key'   => sprintf(
                        'attachments.id:%d',
                        $id
                    ),
                ],
                $changes
            );
        }
    }

    public function test_remove_json_item()
    {
        $column = $this->faker->randomElement(self::$jsonColumns);

        $jsonData = [];
        $countElements = $this->faker->numberBetween(1, 2);
        foreach (range(0, $countElements) as $i) {
            $jsonData[] = collect($this->inputData[$column][0])
                ->merge([
                    'index' => $i,
                ])->toArray();
        }

        $this->contract->update([
            $column     => json_encode($jsonData),
        ]);
        $this->inputData[$column] = $jsonData;

        $removeIndex = $this->faker->numberBetween(0, $countElements - 1);
        unset($this->inputData[$column][$removeIndex]);

        $changes = $this->getChanges();

        $this->assertCount(1, $changes);

        $lookupKey = sprintf(
            '%s.index:%d',
            $column,
            $removeIndex
        );
        $expected = [
            'type'      => 'remove',
            'key'       => $lookupKey,
        ];

        $this->assertEquals($expected, $changes[0]);
    }

    public function test_multiple_json_changes()
    {
        $column = $this->faker->randomElement(self::$jsonColumns);
        $originalData = [];
        $totalElements = $this->faker->numberBetween(3, 4);
        $key = $this->faker->word;
        foreach (range(0, $totalElements) as $i) {
            $originalData[] = [
                'index' => $i,
                $key  => $this->faker->word,
            ];
        }

        $this->contract->update([
            $column => json_encode($originalData)
        ]);

        $newData = $originalData;

        $newCollection = collect($newData);

        //  Remove one
        $removeIndex = $this->faker->numberBetween(0, $totalElements);
        $newCollection = $newCollection->reject(function($item) use ($removeIndex) {
            return $item['index'] == $removeIndex;
        });

        //  Modify one
        do {
            $modifyIndex = $this->faker->numberBetween(0, $totalElements);
        } while ($modifyIndex == $removeIndex);
        $newCollection = $newCollection->map(function($item) use ($modifyIndex, $key) {
            if ($item['index'] == $modifyIndex) {
                $item[$key] = 'horse';
            }
            return $item;
        });

        //  Add one
        $newCollection->push([
            $key    => $this->faker->word,
        ]);

        $this->inputData[$column] = $newCollection->toArray();

        $changes = $this->getChanges();

        $this->assertCount(3, $changes);

        $this->assertEquals(
            1,
            collect($changes)
                ->where('key', $column)
                ->where('type', 'add')
                ->count()
        );

        $this->assertEquals(
            1,
            collect($changes)
                ->where(
                    'key',
                    sprintf(
                        '%s.index:%d.%s',
                        $column,
                        $modifyIndex,
                        $key
                    )
                )->where(
                    'type',
                    'modify'
                )->count()
        );

        $this->assertEquals(
            1,
            collect($changes)
                ->where(
                    'key',
                    sprintf(
                        '%s.index:%d',
                        $column,
                        $removeIndex
                    )
                )
                ->where(
                    'type',
                    'remove'
                )->count()
        );
    }

    public function test_remove_room_item()
    {
        foreach (range(0, 2) as $i) {
            factory(RoomSet::class)->create([
                'contract_id'   => $this->contract->id,
            ]);
        }
        $this->convertContractToInputData();

        $removeId = $this->faker->randomElement($this->contract->roomSets()->pluck('id')->toArray());
        $this->inputData['rooms'] = collect($this->inputData['rooms'])
            ->reject(function($item) use ($removeId) {
                return $item['id'] == $removeId;
            })
            ->toArray();

        $changes = $this->getChanges();

        $this->assertCount(1, $changes);

        $expected = [
            'type'  => 'remove',
            'key'   => sprintf('rooms.id:%d', $removeId),
        ];
        $this->assertEquals($expected, $changes[0]);
    }

    public function test_modify_meta_field()
    {
        $meta = [
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

        $changeKey = $this->faker->randomElement($meta);
        $changeTo = $this->faker->word;
        $this->inputData[$changeKey] = $changeTo;

        $changes = $this->getChanges();

        $this->assertCount(1, $changes);

        $isDate = in_array(
            $changeKey,
            [
                'start_date',
                'end_date',
                'check_in_date',
                'check_out_date',
            ]
        );
        $expectedOriginal = ($isDate) ? $this->contract->$changeKey->format('Y-m-d') : $this->contract->$changeKey;

        $expected = [
            'type'  => 'modify',
            'key'   => $changeKey,
            'original'  => $expectedOriginal,
            'proposed'  => $changeTo,
        ];
        $this->assertEquals($expected, $changes[0]);
    }

    /**
     * Special test to make sure that null values in inner json pairs are not
     * counted as changes
     */
    public function test_modify_json_column_nulls_are_skipped()
    {
        $jsonData = [];
        $keys = [];
        for ($i = 0; $i < $this->faker->numberBetween(1, 2); $i++) {
            $keys[] = $this->faker->word;
        }
        $countElements = $this->faker->numberBetween(1, 2);
        foreach (range(0, $countElements) as $i) {
            $thisElement = [];
            foreach ($keys as $key) {
                $thisElement[$key] = null;
            }
            $thisElement['index'] = $i;
            $jsonData[] = $thisElement;
        }

        $changeColumn = 'meeting_spaces';//$this->faker->randomElement(self::$jsonColumns);

        $this->contract->update([
            $changeColumn   => json_encode($jsonData),
        ]);
        $this->inputData[$changeColumn] = $jsonData;

        $changes = $this->getChanges();

        $this->assertCount(0, $changes);
    }

    public function test_modify_json_column()
    {
        $jsonData = [];
        $keys = [];
        for ($i = 0; $i < $this->faker->numberBetween(1, 2); $i++) {
            $keys[] = $this->faker->word;
        }
        $countElements = $this->faker->numberBetween(1, 2);
        foreach (range(0, $countElements) as $i) {
            $thisElement = [];
            foreach ($keys as $key) {
                $thisElement[$key] = $this->faker->word;
            }
            $thisElement['index'] = $i;
            $jsonData[] = $thisElement;
        }

        $changeColumn = $this->faker->randomElement(self::$jsonColumns);

        $this->contract->update([
            $changeColumn   => json_encode($jsonData),
        ]);

        $changeTo = $this->faker->word;
        $changeIndex = $this->faker->numberBetween(0, $countElements - 1);
        $changeKey = $this->faker->randomElement($keys);

        $this->inputData[$changeColumn] = $jsonData;
        $this->inputData[$changeColumn][$changeIndex][$changeKey] = $changeTo;

        $changes = $this->getChanges();

        $lookupKey = sprintf(
            '%s.index:%d.%s',
            $changeColumn,
            $changeIndex,
            $changeKey
        );
        $expected = [
            'key'   => $lookupKey,
            'type'  => 'modify',
            'original'  => $jsonData[$changeIndex][$changeKey],
            'proposed'  => $changeTo,
        ];
        $this->assertEquals($expected, $changes[0]);
    }

    public function test_modify_term_description()
    {
        $groupIndex = $this->faker->randomElement(array_keys($this->inputData['term_groups']));
        $groupId = $this->inputData['term_groups'][$groupIndex]['id'];
        $termIndex = $this->faker->randomElement(array_keys($this->inputData['term_groups'][$groupIndex]['terms']));
        $termId = $this->inputData['term_groups'][$groupIndex]['terms'][$termIndex]['id'];

        $changeTo = $this->faker->paragraph;

        $this->inputData['term_groups'][$groupIndex]['terms'][$termIndex]['description'] = $changeTo;

        $changes = $this->getChanges();

        $this->assertCount(1, $changes);

        $originalValue = $this->contract->termGroups()
            ->where('id', $groupId)
            ->first()
            ->terms()
            ->where('id', $termId)
            ->first()
            ->description;

        $expected = [
            'type'  => 'modify',
            'key'   => sprintf(
                'term_groups.id:%d.terms.id:%d.description',
                $groupId,
                $termId
            ),
            'original'  => $originalValue,
            'proposed'  => json_encode(['description' => $changeTo]),
        ];
        $this->assertEquals($expected, $changes[0]);
    }

    public function test_add_reservation_method()
    {
        $id = $this->faker->numberBetween(1, 1000);
        $this->inputData['reservation_methods'][] = $id;

        $changes = $this->getChanges();

        $this->assertCount(1, $changes);

        $expected = [
            'type'  => 'add',
            'key'   => 'reservation_methods',
            'proposed'  => $id,
        ];
        $this->assertEquals($expected, $changes[0]);
    }

    public function test_add_payment_method()
    {
        $id = $this->faker->numberBetween(1, 1000);
        $this->inputData['payment_methods'][] = $id;

        $changes = $this->getChanges();

        $this->assertCount(1, $changes);

        $expected = [
            'type'  => 'add',
            'key'   => 'payment_methods',
            'proposed'  => $id,
        ];
        $this->assertEquals($expected, $changes[0]);
    }

    public function test_modify_term_title()
    {
        $groupIndex = $this->faker->randomElement(array_keys($this->inputData['term_groups']));
        $groupId = $this->inputData['term_groups'][$groupIndex]['id'];
        $termIndex = $this->faker->randomElement(array_keys($this->inputData['term_groups'][$groupIndex]['terms']));
        $termId = $this->inputData['term_groups'][$groupIndex]['terms'][$termIndex]['id'];

        $changeTo = $this->faker->word;

        $this->inputData['term_groups'][$groupIndex]['terms'][$termIndex]['title'] = $changeTo;

        $changes = $this->getChanges();

        $this->assertCount(1, $changes);

        $originalValue = $this->contract->termGroups()
            ->where('id', $groupId)
            ->first()
            ->terms()
            ->where('id', $termId)
            ->first()
            ->title;

        $expected = [
            'type'  => 'modify',
            'key'   => sprintf(
                'term_groups.id:%d.terms.id:%d.title',
                $groupId,
                $termId
            ),
            'original'  => $originalValue,
            'proposed'  => json_encode(['title' => $changeTo]),
        ];
        $this->assertEquals($expected, $changes[0]);
    }

    public function test_modify_term_group_name()
    {
        $changeIndex = $this->faker->randomElement(array_keys($this->inputData['term_groups']));
        $changeId = $this->inputData['term_groups'][$changeIndex]['id'];
        $changeTo = $this->faker->word;

        $this->inputData['term_groups'][$changeIndex]['name'] = $changeTo;

        $changes = $this->getChanges();

        $this->assertCount(1, $changes);

        $originalValue = $this->contract->termGroups()
            ->where('id', $changeId)
            ->first()
            ->name;

        $expected = [
            'type'  => 'modify',
            'key'   => sprintf(
                'term_groups.id:%d.name',
                $changeId
            ),
            'original'  => $originalValue,
            'proposed'  => json_encode(['name' => $changeTo]),
        ];
        $this->assertEquals($expected, $changes[0]);
    }

    public function test_remove_term_group()
    {
        $changeIndex = $this->faker->randomElement(array_keys($this->inputData['term_groups']));
        $changeId = $this->inputData['term_groups'][$changeIndex]['id'];

        $this->inputData['term_groups'] = collect($this->inputData['term_groups'])
            ->reject(function($item) use ($changeId) {
                return $item['id'] == $changeId;
            })->toArray();

        $changes = $this->getChanges();

        $expected = [
            'type'  => 'remove',
            'key'   => sprintf(
                'term_groups.id:%d',
                $changeId
            ),
        ];
        $this->assertEquals($expected, $changes[0]);
    }

    public function test_remove_single_term()
    {
        $groupIndex = $this->faker->randomElement(array_keys($this->inputData['term_groups']));
        $groupId = $this->inputData['term_groups'][$groupIndex]['id'];
        $termIndex = $this->faker->randomElement(array_keys($this->inputData['term_groups'][$groupIndex]['terms']));
        $termId = $this->inputData['term_groups'][$groupIndex]['terms'][$termIndex]['id'];

        $terms = &$this->inputData['term_groups'][$groupIndex]['terms'];
        $terms = collect($terms)
            ->reject(function($term) use ($termId) {
                return $term['id'] == $termId;
            })->toArray();

        $changes = $this->getChanges();

        $this->assertCount(1, $changes);

        $expected = [
            'type'  => 'remove',
            'key'   => sprintf(
                'term_groups.id:%d.terms.id:%d',
                $groupId,
                $termId
            )
        ];
        $this->assertEquals($expected, $changes[0]);
    }

    public function test_remove_reservation_method()
    {
        $methodId = $this->faker->randomElement(
            $this->contract->reservationMethods()->pluck('reservation_methods.id')->toArray()
        );

        $this->inputData['reservation_methods'] = collect($this->inputData['reservation_methods'])
            ->reject(function($id) use ($methodId) {
                return $id == $methodId;
            })->toArray();

        $changes = $this->getChanges();

        $this->assertCount(1, $changes);

        $key = sprintf(
            'reservation_methods.id:%d',
            $methodId
        );
        $expected = [
            'type'  => 'remove',
            'key'   => $key,
        ];
        $this->assertEquals($expected, $changes[0]);
    }

    public function test_remove_payment_method()
    {
        $methodId = $this->faker->randomElement(
            $this->contract->paymentMethods()->pluck('payment_methods.id')->toArray()
        );

        $this->inputData['payment_methods'] = collect($this->inputData['payment_methods'])
            ->reject(function($id) use ($methodId) {
                return $id == $methodId;
            })->toArray();

        $changes = $this->getChanges();

        $this->assertCount(1, $changes);

        $key = sprintf(
            'payment_methods.id:%d',
            $methodId
        );
        $expected = [
            'type'  => 'remove',
            'key'   => $key,
        ];
        $this->assertEquals($expected, $changes[0]);
    }

    public function test_modify_room_column()
    {
        $columnNames = [
            'rate'  => 'rate',
            'rooms' => 'rooms_offered',
            'name'  => 'name',
            'description'   => 'description',
            'date'          => 'reservation_date',
        ];

        $changeTo = $this->faker->word;
        $changeColumn = $this->faker->randomElement(array_keys($columnNames));
        $changeIndex = $this->faker->numberBetween(0, $this->contract->roomSets()->count() - 1);
        $changeId = $this->inputData['rooms'][$changeIndex]['id'];

        $this->inputData['rooms'][$changeIndex][$changeColumn] = $changeTo;

        $changes = $this->getChanges();

        $this->assertCount(1, $changes);

        $originalValue = ($changeColumn == 'date')
            ? $this->contract->roomSets[$changeIndex]->reservation_date->format('Y-m-d')
            : $this->contract->roomSets[$changeIndex]->{$columnNames[$changeColumn]};
        $key = sprintf(
            'rooms.id:%d.%s',
            $changeId,
            $changeColumn
        );
        $expected = [
            'type'  => 'modify',
            'key'   => $key,
            'original'  => $originalValue,
            'proposed'  => $changeTo,
        ];
        $this->assertEquals($expected, $changes[0]);
    }

    public function test_add_to_json_array()
    {
        $this->faker->randomElement(self::$jsonColumns);
        $dummyData = $this->getDummyArray();

        $column = $this->faker->randomElement(self::$jsonColumns);
        $this->inputData[$column][] = $dummyData;

        $changes = $this->getChanges();

        $this->assertCount(1, $changes);

        $expected = [
            'type'  => 'add',
            'key'   => $column,
            'proposed'  => json_encode($dummyData),
        ];
        $this->assertEquals($expected, $changes[0]);
    }

    public function test_add_room_set()
    {
        $dummyData = $this->getDummyArray();

        $this->inputData['rooms'][] = $dummyData;

        $changes = $this->getChanges();

        $this->assertCount(1, $changes);

        $expected = [
            'type'  => 'add',
            'key'   => 'rooms',
            'proposed'  => json_encode($dummyData),
        ];
        $this->assertEquals($expected, $changes[0]);
    }

    public function test_add_term_group()
    {
        $dummyData = $this->getDummyArray();

        $this->inputData['term_groups'][] = $dummyData;

        $changes = $this->getChanges();

        $this->assertCount(1, $changes);

        $expected = [
            'type'  => 'add',
            'key'   => 'term_groups',
            'proposed'  => json_encode($dummyData),
        ];
        $this->assertEquals($expected, $changes[0]);
    }

    public function test_add_single_term()
    {
        $groupIndex = $this->faker->randomElement(array_keys($this->inputData['term_groups']));
        $groupId = $this->inputData['term_groups'][$groupIndex]['id'];

        $dummyData = $this->getDummyArray();

        $this->inputData['term_groups'][$groupIndex]['terms'][] = $dummyData;

        $changes = $this->getChanges();

        $this->assertCount(1, $changes);

        $expected = [
            'type'  => 'add',
            'key'   => sprintf(
                'term_groups.id:%d.terms',
                $groupId
            ),
            'proposed'  => json_encode($dummyData),
        ];
        $this->assertEquals($expected, $changes[0]);
    }

    /**
     * Parse changes
     *
     * @return array
     */
    private function getChanges()
    {
        return app(ChangeOrderParser::class)
            ->parseChanges(
                $this->contract->id,
                $this->inputData,
                $this->addAttachments,
                $this->removeAttachments
            );
    }

}
