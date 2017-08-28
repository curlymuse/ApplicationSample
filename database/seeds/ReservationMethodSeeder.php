<?php

use Illuminate\Database\Seeder;
use App\Models\ReservationMethod;

class ReservationMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(ReservationMethod::class)->create([
            'title' => 'Rooming list',
        ]);
        factory(ReservationMethod::class)->create([
            'title' => 'Call in',
        ]);
        factory(ReservationMethod::class)->create([
            'title' => 'Booking portal',
        ]);
    }
}
