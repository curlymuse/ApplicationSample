<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChanceGoogleIdToPlaceIdInPlannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('planners', function(Blueprint $table)
        {
            $table->renameColumn('google_id', 'place_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('planners', function(Blueprint $table)
        {
            $table->renameColumn('place_id', 'google_id');
        });
    }
}
