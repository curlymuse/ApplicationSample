<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDecimalPlacesOnGoogleLatLongHotelsTable extends Migration
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
            $table->decimal('google_latitude', 10, 8)->nullable()->change();
            $table->decimal('google_longitude', 10, 8)->nullable()->change();
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
            $table->float('google_latitude')->nullable()->change();
            $table->float('google_longitude')->nullable()->change();
        });
    }
}
