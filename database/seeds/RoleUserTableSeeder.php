<?php

use Illuminate\Database\Seeder;

class RoleUserTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        \DB::table('role_user')->delete();

        \DB::table('role_user')->insert(array (
            0 =>
                array (
                    'user_id' => '1',
                    'role_id' => '6',
                    'rolable_id' => '1',
                    'rolable_type' => 'App\Models\Licensee',
                    'created_at' => '2016-08-09 15:13:27',
                    'updated_at' => '2016-08-09 15:13:27',
                ),
        ));

        \DB::table('role_user')->insert(array (
            0 =>
                array (
                    'user_id' => '2',
                    'role_id' => '7',
                    'created_at' => '2016-08-09 15:13:27',
                    'updated_at' => '2016-08-09 15:13:27',
                ),
        ));

        \DB::table('role_user')->insert(array (
            0 =>
                array (
                    'user_id' => '3',
                    'role_id' => '7',
                    'created_at' => '2016-08-09 15:13:27',
                    'updated_at' => '2016-08-09 15:13:27',
                ),
        ));

        \DB::table('role_user')->insert(array (
            0 =>
                array (
                    'user_id' => '4',
                    'role_id' => '6',
                    'rolable_id' => '1',
                    'rolable_type' => 'App\Models\Licensee',
                    'created_at' => '2016-08-09 15:13:27',
                    'updated_at' => '2016-08-09 15:13:27',
                ),
        ));

        \DB::table('role_user')->insert(array (
            0 =>
                array (
                    'user_id' => '5',
                    'role_id' => '5',
                    'rolable_id' => '1',
                    'rolable_type' => 'App\Models\Licensee',
                    'created_at' => '2016-08-09 15:13:27',
                    'updated_at' => '2016-08-09 15:13:27',
                ),
        ));

        \DB::table('role_user')->insert(array (
            0 =>
                array (
                    'user_id' => '6',
                    'role_id' => '9',
                    'rolable_id' => '1',
                    'rolable_type' => 'App\Models\Planner',
                    'created_at' => '2016-08-09 15:13:27',
                    'updated_at' => '2016-08-09 15:13:27',
                ),
        ));

        \DB::table('role_user')->insert(array (
            0 =>
                array (
                    'user_id' => '7',
                    'role_id' => '4',
                    'rolable_id' => '1',
                    'rolable_type' => 'App\Models\Client',
                    'created_at' => '2016-08-09 15:13:27',
                    'updated_at' => '2016-08-09 15:13:27',
                ),
        ));

        if (! \App::environment(['production', 'staging'])) {
            \DB::table('role_user')->insert(array (
                0 =>
                    array (
                        'user_id' => '8',
                        'role_id' => '2', // Hotelier
                        'rolable_id' => '1', // Hotel id (actually ignored by code as `hotel_user` is used)
                        'rolable_type' => 'App\Models\Hotel', // (actually ignored by code as `hotel_user` is used)
                        'created_at' => '2016-08-09 15:13:27',
                        'updated_at' => '2016-08-09 15:13:27',
                    ),
            ));

            \DB::table('role_user')->insert(array (
                0 =>
                    array (
                        'user_id' => '9',
                        'role_id' => '3', // GSO
                        'rolable_id' => '1', // Brand Id
                        'rolable_type' => 'App\Models\Brand',
                        'created_at' => '2016-08-09 15:13:27',
                        'updated_at' => '2016-08-09 15:13:27',
                    ),
            ));
        }
    }
}
