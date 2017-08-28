<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndicesToHotelsTable extends Migration
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
            $table->index('latitude');
        });
        Schema::table('hotels', function(Blueprint $table)
        {
            $table->index('longitude');
        });
        Schema::table('hotels', function(Blueprint $table)
        {
            $table->index('name');
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
            $table->dropIndex('latitude');
        });
        Schema::table('hotels', function(Blueprint $table)
        {
            $table->dropIndex('longitude');
        });
        Schema::table('hotels', function(Blueprint $table)
        {
            $table->dropIndex('name');
        });
    }
}
