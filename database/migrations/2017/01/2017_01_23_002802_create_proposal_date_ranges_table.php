<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProposalDateRangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proposal_date_ranges', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('proposal_id')->unsigned()->index();
            $table->integer('event_date_range_id')->unsigned()->index();
            $table->integer('declined_by_user')->unsigned()->index()->nullable();
            $table->string('declined_by_user_type')->nullable();
            $table->datetime('declined_at')->nullable();
            $table->text('declined_because')->nullable();
            $table->integer('submitted_by_user')->unsigned()->index()->nullable();
            $table->datetime('submitted_at')->nullable();
            $table->text('rooms')->nullable();
            $table->text('meeting_spaces')->nullable();
            $table->text('food_and_beverage_spaces')->nullable();
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
        Schema::drop('proposal_date_ranges');
    }
}
