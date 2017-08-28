<?php

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Client::class)->create([
            'place_id'      => 'ChIJixLu7DBu5kcRQnIpA2tErS8',
            'name'          => 'Google',
            'address1'      => '8 Rue de Londres',
            'address2'      => '',
            'city'          => 'Paris',
            'state'         => '',
            'zip'           => '75009',
            'country'       => 'FR'
        ]);
    }
}
