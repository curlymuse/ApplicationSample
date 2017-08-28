<?php

use App\Models\Hotel;
use App\Models\Licensee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class BackendTestingSeeder extends Seeder
{

    /**
     * This file is to trick the db test utility into thinking that there is something to actually seed
     *
     * @return void
     */
    public function run()
    {
        // Create a basic user. It will have an id of 1
        $user = factory(User::class)->create();

        $user->roles()->attach(
            factory(Role::class)->create()
        );

        // Create a licensee. Licensee will have id of 1
        factory(Licensee::class)->create();

        // Create Hotel
        factory(Hotel::class)->create();
    }
}
