<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropProposalSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('proposal_submissions');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('proposal_submissions', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('proposal_id')->unsigned()->index();
            $table->integer('event_date_range_id')->unsigned()->index();
            $table->integer('submitted_by_user')->unsigned()->index();
            $table->softDeletes();
            $table->timestamps();
        });
    }
}
