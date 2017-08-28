<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpaceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('space_requests', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('event_date_range_id')->unsigned()->index();
            $table->date('date_requested');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('type')->default('Meeting');
            $table->string('room_type')->nullable();
            $table->string('name')->nullable();
            $table->integer('attendees')->unsigned()->nullable();
            $table->decimal('budget')->nullable();
            $table->string('budget_units')->nullable();
            $table->string('layout')->nullable();
            $table->text('requests')->nullable();
            $table->text('equipment')->nullable();
            $table->string('meal')->nullable();
            $table->text('notes')->nullable();
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
        Schema::drop('space_requests');
    }
}
