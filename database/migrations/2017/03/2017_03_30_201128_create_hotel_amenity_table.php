<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotelAmenityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amenity_hotel', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('hotel_id')->unsigned()->index();
            $table->integer('amenity_id')->unsigned()->index();
            $table->integer('attribute_id')->unsigned()->index();
            $table->integer('amenity_count')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('amenity_hotel');
    }
}
