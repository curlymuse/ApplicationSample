<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('roles')->delete();

        \DB::table('roles')->insert(array (
            0 =>
            array (
                'id' => 1,
                'slug' => 'guest',
                'name' => 'Guest',
                'created_at' => '2016-08-09 15:13:27',
                'updated_at' => '2016-08-09 15:13:27',
            ),
            1 =>
            array (
                'id' => 2,
                'slug' => 'hotelier',
                'name' => 'Hotelier',
                'created_at' => '2016-08-09 15:13:27',
                'updated_at' => '2016-08-09 15:13:27',
            ),
            2 =>
            array (
                'id' => 3,
                'slug' => 'hotelso',
                'name' => 'Hotel Org',
                'created_at' => '2016-08-09 15:13:27',
                'updated_at' => '2016-08-09 15:13:27',
            ),
            3 =>
            array (
                'id' => 4,
                'slug' => 'client',
                'name' => 'Client',
                'created_at' => '2016-08-09 15:13:27',
                'updated_at' => '2016-08-09 15:13:27',
            ),
            4 =>
            array (
                'id' => 5,
                'slug' => 'licensee-staff',
                'name' => 'Licensee Staff',
                'created_at' => '2016-08-09 15:13:27',
                'updated_at' => '2016-08-09 15:13:27',
            ),
            5 =>
            array (
                'id' => 6,
                'slug' => 'licensee-admin',
                'name' => 'Licensee Admin',
                'created_at' => '2016-08-09 15:13:27',
                'updated_at' => '2016-08-09 15:13:27',
            ),
            6 =>
            array (
                'id' => 7,
                'slug' => 'admin',
                'name' => 'ResBeat Admin',
                'created_at' => '2016-08-09 15:13:27',
                'updated_at' => '2016-08-09 15:13:27',
            ),
            7 =>
            array (
                'id' => 8,
                'slug' => 'independent-contractor',
                'name' => 'Independent Contractor',
                'created_at' => '2016-09-07 14:39:00',
                'updated_at' => '2016-09-07 14:39:00',
            ),
            8 =>
            array (
                'id' => 9,
                'slug' => 'planner',
                'name' => 'Planner',
                'created_at' => '2016-09-07 14:39:00',
                'updated_at' => '2016-09-07 14:39:00',
            )
        ));
    }
}
