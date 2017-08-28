<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProposalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proposals', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('proposal_request_id')->unsigned()->index();
            $table->integer('hotel_id')->unsigned()->index();
            $table->integer('submitted_by_user')->unsigned()->index()->nullable();
            $table->datetime('submitted_at')->nullable();
            $table->float('commission')->nullable();
            $table->float('rebate')->nullable();
            $table->float('additional_charge_per_adult')->nullable();
            $table->float('tax_rate')->nullable();
            $table->integer('min_age_to_check_in')->nullable();
            $table->float('additional_fees')->nullable();
            $table->string('additional_fees_units')->nullable();
            $table->date('honor_bid_until')->nullable();
            $table->text('deposit_policy')->nullable();
            $table->text('cancellation_policy')->nullable();
            $table->integer('cancellation_policy_days')->nullable();
            $table->string('cancellation_policy_file')->nullable();
            $table->text('notes')->nullable();
            $table->text('attachments')->nullable();
            $table->text('questions')->nullable();
            $table->text('date_ranges');
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
        Schema::drop('proposals');
    }
}
