<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Temporarily increase memory limit
        ini_set('memory_limit','2048M');

        DB::table('clauses')->truncate();
        DB::table('clients')->truncate();
        DB::table('planners')->truncate();
        DB::table('licensees')->truncate();
        DB::table('hotels')->truncate();
        DB::table('users')->truncate();
        DB::table('brands')->truncate();
        DB::table('role_user')->truncate();
        DB::table('roles')->truncate();
        DB::table('event_date_range_proposal')->truncate();
        DB::table('event_date_ranges')->truncate();
        DB::table('event_types')->truncate();
        DB::table('events')->truncate();

        $this->call(ClientsTableSeeder::class);
        $this->call(PlannersTableSeeder::class);
        $this->call(LicenseesTableSeeder::class);
        $this->call(HotelsTableSeeder::class);
        $this->call(BrandsTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(RoleUserTableSeeder::class);
        $this->call(EventTypesTableSeeder::class);
    }
}
