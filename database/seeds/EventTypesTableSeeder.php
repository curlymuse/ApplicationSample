<?php

use Illuminate\Database\Seeder;

class EventTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('event_types')->delete();

        \DB::table('event_types')->insert([
            [
                'id' => 1,
                'parent_id' => null,
                'name' => 'Professional Sports',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 2,
                'parent_id' => 1,
                'name' => 'Motorsports',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 3,
                'parent_id' => 1,
                'name' => 'Football',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 4,
                'parent_id' => 1,
                'name' => 'Hockey',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 5,
                'parent_id' => 1,
                'name' => 'Baseball',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 6,
                'parent_id' => 1,
                'name' => 'Basketball',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 7,
                'parent_id' => 1,
                'name' => 'Soccer',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 8,
                'parent_id' => 1,
                'name' => 'Other',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 9,
                'parent_id' => null,
                'name' => 'Amateur Sports',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 10,
                'parent_id' => 9,
                'name' => 'Motorsports',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 11,
                'parent_id' => 9,
                'name' => 'Football',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 12,
                'parent_id' => 9,
                'name' => 'Hockey',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 13,
                'parent_id' => 9,
                'name' => 'Baseball',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 14,
                'parent_id' => 9,
                'name' => 'Basketball',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 15,
                'parent_id' => 9,
                'name' => 'Soccer',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 16,
                'parent_id' => 9,
                'name' => 'Other',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 17,
                'parent_id' => null,
                'name' => 'Corporate',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 18,
                'parent_id' => 17,
                'name' => 'Business Meeting',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 19,
                'parent_id' => 17,
                'name' => 'Conference',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 20,
                'parent_id' => 17,
                'name' => 'Association',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 21,
                'parent_id' => 17,
                'name' => 'User Group',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 22,
                'parent_id' => 17,
                'name' => 'Charity Event',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 23,
                'parent_id' => 17,
                'name' => 'Conference Convention',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 24,
                'parent_id' => 17,
                'name' => 'Corporate Incentive Travel',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 25,
                'parent_id' => 17,
                'name' => 'Technology/Marketing',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 26,
                'parent_id' => 17,
                'name' => 'Tour Operator Group',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 27,
                'parent_id' => 17,
                'name' => 'Non-Profit',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 28,
                'parent_id' => null,
                'name' => 'SMERF',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 29,
                'parent_id' => 28,
                'name' => 'Bachelor/ette Party',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 30,
                'parent_id' => 28,
                'name' => 'Birthday Party',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 31,
                'parent_id' => 28,
                'name' => 'Reunion',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 32,
                'parent_id' => 28,
                'name' => 'Religious',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 33,
                'parent_id' => 28,
                'name' => 'Wedding',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 34,
                'parent_id' => 28,
                'name' => 'Other',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 35,
                'parent_id' => 28,
                'name' => 'Fraternity/Sorority',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 36,
                'parent_id' => 28,
                'name' => 'Government/Military',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 37,
                'parent_id' => 28,
                'name' => 'Graduation',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 38,
                'parent_id' => 28,
                'name' => 'Holiday',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 39,
                'parent_id' => null,
                'name' => 'Festival',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 40,
                'parent_id' => 39,
                'name' => 'Music',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 41,
                'parent_id' => 39,
                'name' => 'Food/Wine',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
            [
                'id' => 42,
                'parent_id' => 39,
                'name' => 'Other',
                'created_at' => '2016-10-06 00:00:00',
                'updated_at' => '2016-10-06 00:00:00',
            ],
        ]);
    }
}
