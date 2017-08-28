<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class)->create([
            'name'          => 'Robin Arenson',
            'email'         => 'robin.arenson@gmail.com',
            'password'      => bcrypt(123)
        ]);

        factory(User::class)->create([
            'name'          => 'Hrach Tadevosyan',
            'email'         => 'tadevosyanhrach@gmail.com',
            'password'      => bcrypt('HrachTest')
        ]);

        factory(User::class)->create([
            'name'          => 'Nate Ritter',
            'email'         => 'nate.ritter@resbeat.com',
            'password'      => bcrypt('secret')
        ]);

        factory(User::class)->create([
            'name'          => 'Joe LicenseeAdmin',
            'email'         => 'joe@licensee-admin.com',
            'password'      => bcrypt('secret')
        ]);

        factory(User::class)->create([
            'name'          => 'Joe LicenseeStaff',
            'email'         => 'joe@licensee-staff.com',
            'password'      => bcrypt('secret')
        ]);

        factory(User::class)->create([
            'name'          => 'Frank Planner',
            'email'         => 'mr@planner.com',
            'password'      => bcrypt('secret')
        ]);

        factory(User::class)->create([
            'name'          => 'Suzy Client',
            'email'         => 'mrs@client.com',
            'password'      => bcrypt('secret')
        ]);

        if (! \App::environment(['production', 'staging'])) {
            factory(User::class)->create([
                'name'          => 'Harry Hotelier',
                'email'         => 'harry@hotelier.com',
                'password'      => bcrypt('secret')
            ]);

            factory(User::class)->create([
                'name'          => 'Gisele TheGSO',
                'email'         => 'gisele@gso.com',
                'password'      => bcrypt('secret')
            ]);
        }
    }
}
