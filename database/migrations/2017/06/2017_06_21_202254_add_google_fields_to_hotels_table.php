<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGoogleFieldsToHotelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hotels', function(Blueprint $table)
        {
            $table->string('place_id')->nullable();
            $table->decimal('google_stars')->nullable();
            $table->float('google_latitude')->nullable();
            $table->float('google_longitude')->nullable();
            $table->datetime('google_updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hotels', function(Blueprint $table)
        {
            $table->dropColumn('place_id');
            $table->dropColumn('google_stars');
            $table->dropColumn('google_latitude');
            $table->dropColumn('google_longitude');
            $table->dropColumn('google_updated_at');
        });
    }
}
