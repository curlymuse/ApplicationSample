<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRebateAndCommissionColumnsToLicenseesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('licensees', function(Blueprint $table)
        {
            $table->string('default_currency', 3)->default('USD')->after('company_name');
            $table->decimal('default_commission')->nullable()->after('company_name');
            $table->decimal('default_rebate')->nullable()->after('company_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('licensees', function(Blueprint $table)
        {
            $table->dropColumn('default_currency');
        });

        Schema::table('licensees', function(Blueprint $table)
        {
            $table->dropColumn('default_commission');
        });

        Schema::table('licensees', function(Blueprint $table)
        {
            $table->dropColumn('default_rebate');
        });
    }
}
