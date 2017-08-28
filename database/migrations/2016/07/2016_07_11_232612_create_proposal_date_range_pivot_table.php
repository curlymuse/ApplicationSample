<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProposalDateRangePivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_date_range_proposal', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('event_date_range_id')->unsigned()->index();
            $table->integer('proposal_id')->unsigned()->index();
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
        Schema::drop('event_date_range_proposal');
    }
}
