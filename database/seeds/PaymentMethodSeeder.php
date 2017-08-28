<?php

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(PaymentMethod::class)->create([
            'title' => 'Individual pays own',
        ]);
        factory(PaymentMethod::class)->create([
            'title' => 'R/T to master',
        ]);
        factory(PaymentMethod::class)->create([
            'title' => 'All charges to master',
        ]);
    }
}
