<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotelImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotel_images', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('hotel_id')->unsigned()->index();
            $table->string('main_path');
            $table->string('thumbnail_path');
            $table->string('source_path');
            $table->string('caption')->nullable();
            $table->integer('display_order')->nullable();
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
        Schema::drop('hotel_images');
    }
}
