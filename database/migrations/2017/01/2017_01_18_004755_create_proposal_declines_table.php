<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProposalDeclinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proposal_declines', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('proposal_id')->unsigned()->index();
            $table->integer('event_date_range_id')->unsigned()->index();
            $table->integer('declined_by_user')->unsigned()->index();
            $table->string('declined_by_user_type');
            $table->text('declined_because')->nullable();
            $table->softDeletes();
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
        Schema::drop('proposal_declines');
    }
}
