<?php

use App\Models\Planner;
use Illuminate\Database\Seeder;

class PlannersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Planner::class)->create([
            'place_id'      => 'ChIJb6n9ukrqj1QR5_PL7Qe2Xnc',
            'name'          => 'Port Townsend Event Planning',
            'address1'      => '930 Beckett Point Rd',
            'address2'      => '',
            'city'          => 'Port Townsend',
            'state'         => 'WA',
            'zip'           => '98368',
            'country'       => 'USA'
        ]);
    }
}
