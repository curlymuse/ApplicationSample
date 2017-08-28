<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomDateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_request_dates', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('event_date_range_id')->unsigned()->index();
            $table->string('room_type_name');
            $table->float('preferred_rate_min')->nullable();
            $table->float('preferred_rate_max')->nullable();
            $table->date('room_date');
            $table->integer('rooms_requested')->unsigned();
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
        Schema::drop('room_request_dates');
    }
}
