<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomSetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_sets', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('contract_id')->unsigned()->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('reservation_date');
            $table->integer('rooms_offered')->unsigned();
            $table->float('rate');
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
        Schema::drop('room_sets');
    }
}
