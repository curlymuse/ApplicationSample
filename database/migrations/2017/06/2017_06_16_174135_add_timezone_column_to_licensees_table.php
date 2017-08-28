<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimezoneColumnToLicenseesTable extends Migration
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
            $table->string('timezone')->default('America/Chicago')->after('receive_daily_recap');
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
            $table->dropColumn('timezone');
        });
    }
}
