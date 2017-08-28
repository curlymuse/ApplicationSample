<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDailyRecapSettingColumnToLicenseesTable extends Migration
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
            $table->boolean('receive_daily_recap')->default(false)->after('is_suspended');
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
            $table->dropColumn('receive_daily_recap');
        });
    }
}
