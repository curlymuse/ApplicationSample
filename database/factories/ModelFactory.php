<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

use Carbon\Carbon;

$factory->define(App\Models\Attachment::class, function (Faker\Generator $faker) {
    $class = $faker->randomElement([
        \App\Models\ProposalRequest::class,
        \App\Models\Contract::class,
        \App\Models\Proposal::class,
    ]);
    $attachable = factory($class)->create();
    return [
        'display_name'     => sprintf('%s.%s', $faker->word, $faker->fileExtension),
        'url'              => $faker->url,
        'attachable_id'    => $attachable->id,
        'attachable_type'  => get_class($attachable),
        'uploaded_by_user' => factory(\App\Models\User::class)->create()->id,
        'category'         => $faker->word,
    ];
});

$factory->define(App\Models\Attribute::class, function (Faker\Generator $faker) {
    return [
        'name'  => $faker->word,
        'has_numeric_entry'    => $faker->boolean,
    ];
});

$factory->define(App\Models\Amenity::class, function (Faker\Generator $faker) {
    return [
        'name'  => $faker->word,
        'amenity_type_id'   => factory(App\Models\AmenityType::class)->create()->id,
    ];
});

$factory->define(App\Models\AmenityType::class, function (Faker\Generator $faker) {
    return [
        'name'  => $faker->word,
    ];
});

$factory->define(App\Models\Brand::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->sentence,
        'code' => $faker->randomLetter . $faker->randomLetter,
    ];
});

$factory->define(App\Models\PropertyType::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->sentence,
    ];
});

$factory->define(App\Models\ChangeOrder::class, function (Faker\Generator $faker) {
    return [
        'contract_id'        => factory(\App\Models\Contract::class)->create()->id,
        'initiated_by_party' => $faker->randomElement(['licensee', 'hotel']),
        'initiated_by_user'  => factory(\App\Models\User::class)->create()->id,
    ];
});

$factory->define(App\Models\Clause::class, function (Faker\Generator $faker) {
    return [
        'title'       => $faker->sentence,
        'body'        => $faker->paragraph,
        'is_default'  => $faker->boolean,
        'licensee_id' => factory(\App\Models\Licensee::class)->create()->id,
    ];
});

$factory->define(App\Models\Client::class, function (Faker\Generator $faker) {
    return [
        'name'     => ucwords($faker->word),
        'place_id' => $faker->word . $faker->unique()->numberBetween(1, 100000),
    ];
});

$factory->define(App\Models\Contract::class, function (Faker\Generator $faker) {
    return [
        'proposal_id' => ($proposal = factory(App\Models\Proposal::class)->create())->id,
        'event_date_range_id' => factory(App\Models\EventDateRange::class)->create([
            'event_id'  => $proposal->proposalRequest->event_id,
        ])->id,
        'start_date'                  => $faker->date,
        'end_date'                    => $faker->date,
        'check_in_date'               => $faker->date,
        'check_out_date'              => $faker->date,
        'declined_by_owner_at'     => null,
        'declined_by_hotel_at'        => null,
        'commission'                  => $faker->randomFloat(2, 1, 1000),
        'rebate'                      => $faker->randomFloat(2, 1, 1000),
        'additional_charge_per_adult' => $faker->randomFloat(2, 1, 1000),
        'tax_rate'                    => $faker->randomFloat(2, 1, 1000),
        'min_age_to_check_in'         => $faker->numberBetween(18, 21),
        'min_length_of_stay'          => $faker->numberBetween(1, 3),
        'additional_fees'             => $faker->randomFloat(2, 1, 1000),
        'additional_fees_units'       => $faker->words(2, true),
        'cutoff_date'                 => $faker->date,
        'deposit_policy'              => $faker->paragraph,
        'cancellation_policy'         => $faker->paragraph,
        'cancellation_policy_days'    => $faker->numberBetween(1, 5),
        'cancellation_policy_file'    => $faker->url,
        'notes' => json_encode([
            'accommodations' => $faker->sentence,
            'meeting_space' => $faker->sentence,
            'food_and_beverage' => $faker->sentence,
        ]),
        'questions' => json_encode([
            [
                'index'    => 0,
                'id'       => ($question = factory(\App\Models\RequestQuestion::class)->create())->id,
                'question' => $question->question,
                'answer'   => $faker->sentence,
            ]
        ]),
        'meeting_spaces' => json_encode([
            [
                'index'         => 0,
                'date'          => $faker->date('Y-m-d'),
                'amount'        => $faker->randomFloat(2, 200, 1000),
                'amount_units'  => $faker->randomElement(['total', 'per person']),
                'description'   => $faker->sentence,
                'complimentary' => $faker->boolean,
                'conditions'    => $faker->sentence,
            ]
        ]),
        'food_and_beverage' => json_encode([
            [
                'index'        => 0,
                'date'         => $faker->date('Y-m-d'),
                'amount'       => $faker->randomFloat(2, 200, 1000),
                'amount_units' => $faker->randomElement(['total', 'per person']),
            ]
        ])
    ];
});

$factory->define(App\Models\ContractTerm::class, function (Faker\Generator $faker) {
    return [
        'contract_term_group_id' => factory(App\Models\ContractTermGroup::class)->create()->id,
        'title' => $faker->word,
        'description' => $faker->paragraph,
    ];
});

$factory->define(App\Models\ContractTermGroup::class, function (Faker\Generator $faker) {
    return [
        'contract_id' => factory(App\Models\Contract::class)->create()->id,
        'name' => ucwords(implode(' ', $faker->words(2))),
    ];
});



$factory->define(App\Models\Event::class, function (Faker\Generator $faker) {
    $client = factory(\App\Models\Client::class)->create();
    $event_type = factory(App\Models\EventType::class)->create();
    return [
        'name'           => ucwords($faker->sentence(3)),
        'client_id'      => $client->id,
        'licensee_id'    => factory(App\Models\Licensee::class)->create()->id,
        'event_type_id'  => $event_type->id,
        'event_sub_type_id' => factory(App\Models\EventType::class)->create(['parent_id' => $event_type->id])->id,
        'event_group_id' => factory(App\Models\EventGroup::class)->create([
            'client_id' => $client->id,
        ])->id,
    ];
});

$factory->define(App\Models\EventDateRange::class, function (Faker\Generator $faker) {
    $startDate = new Carbon($faker->dateTimeBetween('+1 month', '+6 months')->format('Y-m-d'));
    $endDate = $startDate->copy()->addDays(5);
    $checkInDate = $startDate->copy()->subDays($faker->numberBetween(0, 3));
    $checkOutDate = $endDate->copy()->addDays($faker->numberBetween(0, 3));

    return [
        'event_id'       => factory(App\Models\Event::class)->create()->id,
        'start_date'     => $startDate,
        'end_date'       => $endDate,
        'check_in_date'  => $checkInDate,
        'check_out_date' => $checkOutDate,
    ];
});

$factory->define(App\Models\EventGroup::class, function (Faker\Generator $faker) {
    return [
        'name'      => $faker->company,
        'client_id' => factory(\App\Models\Client::class)->create()->id,
    ];
});

$factory->define(App\Models\EventLocation::class, function (Faker\Generator $faker) {
    return [
        'name'      => $faker->company,
        'place_id'  => $faker->word . $faker->numberBetween(1, 10000),
        'latitude'  => $faker->randomFloat(2, -70, 70),
        'longitude' => $faker->randomFloat(2, -70, 70),
        'formatted_address' => '123 Main Street, Los Angeles, CA 90042',
        'street_number' => '123 Main Street',
        'locality' => 'Los Angeles',
        'administrative_area_level_1' => 'CA',
        'postal_code' => 12345,
        'country' => 'USA',
    ];
});

$factory->define(App\Models\EventType::class, function (Faker\Generator $faker) {
    return [
        'name'    => ucwords($faker->word),
    ];
});

$factory->define(App\Models\Guest::class, function (Faker\Generator $faker) {
    return [
        'name'           => $faker->name,
        'email'          => $faker->safeEmail,
        'phone'          => $faker->phoneNumber,
        'address' => $faker->address,
        'city' => $faker->city,
        'state' => $faker->state,
        'zip' => $faker->postcode,
        'notes_to_hotel'    => $faker->sentence,
        'notes_internal'    => $faker->sentence,
        'special_requests'    => $faker->sentence,
    ];
});

$factory->define(App\Models\Hotel::class, function (Faker\Generator $faker) {
    return [
        'name' => ucwords(implode(' ', $faker->words(2))) . ' Hotel',
        'brand_id' => factory(App\Models\Brand::class)->create()->id,
        'property_type_id'  => factory(App\Models\PropertyType::class)->create()->id,
        'address1' => $faker->address,
        'address2' => $faker->address,
        'city' => $faker->city,
        'state' => $faker->state,
        'country' => $faker->country,
        'zip' => $faker->postcode,
        'travelocity_rating'         => $faker->numberBetween(1, 5),
        'rate_min'                   => ($rateMin = $faker->randomFloat(2, 100, 800)),
        'rate_max'                   => ($rateMin + $faker->randomFloat(2, 100, 800)),
        'sleeping_rooms'             => $faker->numberBetween(30, 1200),
        'meeting_rooms'              => $faker->numberBetween(30, 1200),
        'largest_meeting_room_sq_ft' => $faker->numberBetween(10000, 20000),
        'total_meeting_room_sq_ft'   => $faker->numberBetween(10000, 200000),
        'floors'                    => $faker->numberBetween(1, 20),
        'year_built'                    => $faker->numberBetween(1900, 2020),
        'year_of_last_renovation'                    => $faker->numberBetween(1900, 2020),
        'property_phone'    => $faker->phoneNumber,
        'property_fax'    => $faker->phoneNumber,
        'property_email'    => $faker->unique()->safeEmail,
        'google_stars'  => $faker->numberBetween(1, 5),
        'google_updated_at' => $faker->dateTime,
        'mobil_star_rating' => $faker->numberBetween(1, 5),
        'latitude'                   => $faker->randomFloat(6, 30, 47),
        'longitude'                  => $faker->randomFloat(6, -75, -20),
    ];
});

$factory->define(App\Models\HotelCorrelation::class, function (Faker\Generator $faker) {
    return [
        'source'    => $faker->word,
        'correlation_id'    => $faker->uuid,
        'hotel_id'  => factory(App\Models\Hotel::class)->create()->id,
    ];
});

$factory->define(App\Models\Licensee::class, function (Faker\Generator $faker) {
    return [
        'company_name'       => $faker->company,
        'timezone'          => $faker->timezone,
        'default_currency'   => 'USD',
        'default_rebate'     => $faker->randomFloat(2, 5, 100),
        'default_commission' => $faker->randomFloat(2, 5, 100),
    ];
});

$factory->define(App\Models\LicenseeQuestion::class, function (Faker\Generator $faker) {
    return [
        'licensee_question_group_id' => factory(App\Models\LicenseeQuestionGroup::class)->create()->id,
        'question'                   => ucwords(implode(' ', $faker->words(2))),
    ];
});

$factory->define(App\Models\LicenseeQuestionGroup::class, function (Faker\Generator $faker) {
    return [
        'licensee_id' => factory(App\Models\Licensee::class)->create()->id,
        'name'        => ucwords(implode(' ', $faker->words(2))),
    ];
});

$factory->define(App\Models\LicenseeTerm::class, function (Faker\Generator $faker) {
    return [
        'licensee_term_group_id' => factory(App\Models\LicenseeTermGroup::class)->create()->id,
        'title' => $faker->word,
        'description' => $faker->paragraph,
    ];
});

$factory->define(App\Models\LicenseeTermGroup::class, function (Faker\Generator $faker) {
    return [
        'licensee_id' => factory(App\Models\Licensee::class)->create()->id,
        'name' => ucwords(implode(' ', $faker->words(2))),
    ];
});

$factory->define(App\Models\LogEntry::class, function (Faker\Generator $faker) {
    return [
        'action'    => $faker->word,
        'account_type'  => ($accountClass = $faker->randomElement([\App\Models\Licensee::class, \App\Models\Hotel::class])),
        'account_id'    => factory($accountClass)->create()->id,
        'user_id'   => factory(\App\Models\User::class)->create()->id,
    ];
});

$factory->define(App\Models\PaymentMethod::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->word,
    ];
});


$factory->define(App\Models\Planner::class, function (Faker\Generator $faker) {
    return [
        'name'     => ucwords($faker->word),
        'place_id' => $faker->word . $faker->unique()->numberBetween(1, 1000),
    ];
});

$factory->define(App\Models\Proposal::class, function (Faker\Generator $faker) {
    return [
        'proposal_request_id'         => ($request = factory(App\Models\ProposalRequest::class)->create())->id,
        'hotel_id'                    => factory(App\Models\Hotel::class)->create()->id,
        'commission'                  => $faker->randomFloat(2, 1, 1000),
        'rebate'                      => $faker->randomFloat(2, 1, 1000),
        'additional_charge_per_adult' => $faker->randomFloat(2, 1, 1000),
        'tax_rate'                    => $faker->randomFloat(2, 1, 1000),
        'min_age_to_check_in'         => $faker->numberBetween(18, 21),
        'additional_fees'             => $faker->randomFloat(2, 1, 1000),
        'additional_fees_units'       => $faker->words(2, true),
        'honor_bid_until'             => $faker->datetime,
        'min_length_of_stay'          => $faker->numberBetween(1, 3),
        'deposit_policy'              => $faker->paragraph,
        'attrition_rate'              => $faker->numberBetween(0, 80),
        'cancellation_policy'         => $faker->paragraph,
        'cancellation_policy_days'    => $faker->numberBetween(1, 5),
        'cancellation_policy_file'    => $faker->url,
        'notes' => json_encode([
            'accommodations' => $faker->sentence,
            'meeting_space' => $faker->sentence,
            'food_and_beverage' => $faker->sentence,
        ]),
        'questions' => json_encode([
            [
                'id'    => factory(\App\Models\RequestQuestion::class)->create()->id,
                'answer'    => $faker->sentence,
            ]
        ]),
    ];
});

$factory->define(App\Models\ProposalDateRange::class, function (Faker\Generator $faker) {
    $eventDateRange = factory(\App\Models\EventDateRange::class)->create();
    return [
        'proposal_id'           => factory(\App\Models\Proposal::class)->create()->id,
        'event_date_range_id'           => $eventDateRange->id,
        //'declined_by_user'      => factory(\App\Models\User::class)->create()->id,
        //'declined_at'           => $faker->dateTime,
        //'declined_by_user_type'      => $faker->randomElement(['hotel', 'licensee']),
        //'declined_because'  => $faker->sentence,
        //'submitted_by_user'      => factory(\App\Models\User::class)->create()->id,
        //'submitted_at'           => $faker->dateTime,
        'rooms' => json_encode([
            [
                'name'  => $faker->words(2, true),
                'date'  => $faker->date('Y-m-d'),
                'rooms'  => $faker->numberBetween(100, 2000),
                'rate'  => $faker->randomFloat(2, 200, 1000),
                'description' => $faker->sentence,
            ]
        ]),
        'meeting_spaces' => json_encode([
            [
                'id'    => factory(\App\Models\SpaceRequest::class)->create([
                    'event_date_range_id'   => $eventDateRange->id,
                ])->id,
                'date'  => $faker->date('Y-m-d'),
                'amount'  => $faker->randomFloat(2, 200, 1000),
                'amount_units' => $faker->randomElement(['total', 'per person']),
                'description' => $faker->sentence,
                'complimentary' => $faker->boolean,
            ]
        ]),
        'food_and_beverage_spaces' => json_encode([
            [
                'id'    => factory(\App\Models\SpaceRequest::class)->create([
                    'event_date_range_id'   => $eventDateRange->id,
                ])->id,
                'date'  => $faker->date('Y-m-d'),
                'amount'  => $faker->randomFloat(2, 200, 1000),
                'amount_units' => $faker->randomElement(['total', 'per person']),
            ]
        ])
    ];
});

$factory->define(App\Models\ProposalDecline::class, function (Faker\Generator $faker) {
    return [
        'proposal_id'           => factory(\App\Models\Proposal::class)->create()->id,
        'event_date_range_id'   => factory(\App\Models\EventDateRange::class)->create()->id,
        'declined_by_user'      => factory(\App\Models\User::class)->create()->id,
        'declined_by_user_type' => $faker->randomElement(['hotel', 'licensee']),
        'declined_because'      => $faker->sentence,
    ];
});

$factory->define(App\Models\ProposalRequest::class, function (Faker\Generator $faker) {
    $client = factory(\App\Models\Client::class)->create();
    $eventGroup = factory(App\Models\EventGroup::class)->create([
        'client_id' => $client->id,
    ]);
    $eventType = factory(\App\Models\EventType::class)->create();
    $eventSubType = factory(\App\Models\EventType::class)->create([
        'parent_id' => $eventType->id,
    ]);
    $event = factory(\App\Models\Event::class)->create([
        'client_id'         => $client->id,
        'event_group_id'    => $eventGroup->id,
        'event_type_id'     => $eventType->id,
        'event_sub_type_id' => $eventSubType->id,
    ]);
    $user = factory(\App\Models\User::class)->create();
    $user->roles()->attach(
        factory(\App\Models\Role::class)->create(['slug' => 'licensee-staff', 'name' => 'Licensee Staff'])->id,
        [
            'rolable_type'  => \App\Models\Licensee::class,
            'rolable_id'    => $event->licensee_id,
        ]
    );
    return [
        'created_by_user'   => $user->id,
        'client_id'   => $client->id,
        'event_id'    => $event->id,
        'planner_id'  => factory(\App\Models\Planner::class)->create()->id,
        'cutoff_date' => Carbon::now()->addDays($faker->numberBetween(1, 100)),
        'is_meeting_space_required'   => $faker->boolean,
        'is_food_and_beverage_required'   => $faker->boolean,
        'is_attrition_acceptable'   => $faker->boolean,
    ];
});

$factory->define(App\Models\RequestHotel::class, function (Faker\Generator $faker) {
    return [
        'proposal_request_id' => factory(App\Models\ProposalRequest::class)->create()->id,
        'hotel_id' => factory(App\Models\Hotel::class)->create()->id,
    ];
});


$factory->define(App\Models\RequestNote::class, function (Faker\Generator $faker) {
    return [
        'proposal_request_id' => factory(App\Models\ProposalRequest::class)->create()->id,
        'author_id'           => factory(App\Models\User::class)->create()->id,
        'body'                => $faker->paragraph(3),
    ];
});

$factory->define(App\Models\RequestQuestion::class, function (Faker\Generator $faker) {
    return [
        'request_question_group_id' => factory(App\Models\RequestQuestionGroup::class)->create()->id,
        'question'                   => ucwords(implode(' ', $faker->words(2))),
    ];
});

$factory->define(App\Models\RequestQuestionGroup::class, function (Faker\Generator $faker) {
    return [
        'proposal_request_id' => factory(App\Models\ProposalRequest::class)->create()->id,
        'name'        => ucwords(implode(' ', $faker->words(2))),
    ];
});

$factory->define(App\Models\Reservation::class, function (Faker\Generator $faker) {
    return [
        'status'    => $faker->randomElement(['unconfirmed', 'confirmed', 'pending cancellation', 'cancelled']),
        'confirmation_number' => $faker->uuid,
        'cancellation_number' =>  $faker->uuid,
        'guest_name' => $faker->name,
        'guest_address' => $faker->address,
        'guest_city' => $faker->city,
        'guest_state' => $faker->state,
        'guest_country' => $faker->country,
        'guest_zip' => $faker->postcode,
        'guest_phone' => $faker->phoneNumber,
        'guest_special_requests'  => $faker->sentence,
        'guest_notes_to_hotel'  => $faker->sentence,
        'guest_notes_internal'  => $faker->sentence,
    ];
});

$factory->define(App\Models\Role::class, function (Faker\Generator $faker) {
    $name = $faker->words(2, true);
    return [
        'slug' => str_slug($name),
        'name' => ucwords($name),
    ];
});

$factory->define(App\Models\ReservationMethod::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->word,
    ];
});

$factory->define(App\Models\RoomRequestDate::class, function (Faker\Generator $faker) {
    $dateRange = factory(App\Models\EventDateRange::class)->create();

    return [
        'event_date_range_id' => $dateRange->id,
        'proposal_request_id'   => factory(App\Models\ProposalRequest::class)->create([
            'event_id'  => $dateRange->event_id,
        ])->id,
        'room_date'           => $faker->dateTimeThisCentury,
        'rooms_requested'     => $faker->numberBetween(20, 80),
        'room_type_name'      => $faker->randomElement(['King', 'Queen', 'Double', 'Suite']),
        'preferred_rate_min'  => ($min = $faker->randomFloat(2, 1, 500)),
        'preferred_rate_max'  => $faker->randomFloat(2, $min, $min + 500),
    ];
});

$factory->define(App\Models\RoomSet::class, function (Faker\Generator $faker) {
    return [
        'contract_id'       => factory(\App\Models\Contract::class)->create()->id,
        'name'          => $faker->word,
        'description'   => $faker->sentence,
        'reservation_date'  => $faker->date('Y-m-d'),
        'rate'  => $this->faker->randomFloat(2, 100, 1000),
        'rooms_offered'     => $faker->numberBetween(1, 1000),
    ];
});

$factory->define(App\Models\SpaceRequest::class, function (Faker\Generator $faker) {

    $startHour = $faker->numberBetween(0, 12);
    $endHour = $startHour + $faker->numberBetween(0, 12);

    $dateRange = factory(App\Models\EventDateRange::class)->create();

    return [
        'event_date_range_id' => $dateRange->id,
        'proposal_request_id'   => factory(App\Models\ProposalRequest::class)->create([
            'event_id'  => $dateRange->event_id,
        ])->id,
        'date_requested'      => $faker->dateTimeThisCentury,
        'type'                => $faker->randomElement(['Meeting', 'Food & Beverage']),
        'name'                => ucwords($faker->words(2, true)),
        'start_time'          => sprintf('%s:00:00', $startHour),
        'end_time'            => sprintf('%s:00:00', $endHour),
        'attendees'           => $faker->numberBetween(50, 500),
        'budget'              => $faker->numberBetween(1000, 3000),
        'budget_units'        => $this->faker->words(3, true),
        'room_type'           => $this->faker->words(3, true),
        'layout'              => $this->faker->words(3, true),
        'requests'            => $this->faker->sentence,
        'equipment' => json_encode([
            [
                'name'     => $faker->word,
                'quantity' => $faker->numberBetween(1, 30),
            ],
            [
                'name'     => $faker->word,
                'quantity' => $faker->numberBetween(1, 30),
            ]
        ])
    ];
});

$factory->define(App\Models\Tag::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
    ];
});


$factory->define(App\Models\Team::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->company,
    ];
});

$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
    return [
        'name'           => $faker->name,
        'email'          => $faker->unique()->safeEmail,
        'phone'          => $faker->phoneNumber,
        'password'       => bcrypt(str_random(10)),
        'hash'          => str_random(),
        'remember_token' => str_random(10),
    ];
});
