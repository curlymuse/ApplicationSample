<?php

use App\Models\Licensee;
use Illuminate\Database\Seeder;

class LicenseesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Licensee::class)->create([
            'company_name' => 'Hotels for Hope',
            'default_currency' => 'USD',
            'default_commission' => 10,
            'default_rebate' => 1,
            'is_suspended' => 0
        ]);
    }
}
