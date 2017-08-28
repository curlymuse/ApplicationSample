<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventDateRangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_date_ranges', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('event_id')->unsigned()->index();
            $table->date('start_date');
            $table->date('end_date');
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->boolean('is_chosen')->default(false);
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
        Schema::drop('event_date_ranges');
    }
}
