<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('proposal_id')->unsigned()->index();
            $table->integer('declined_by_hotel_user')->unsigned()->index()->nullable();
            $table->text('declined_by_hotel_because')->nullable();
            $table->datetime('declined_by_hotel_at')->nullable();
            $table->integer('declined_by_licensee_user')->unsigned()->index()->nullable();
            $table->text('declined_by_licensee_because')->nullable();
            $table->datetime('declined_by_licensee_at')->nullable();
            $table->integer('accepted_by_hotel_user')->unsigned()->index()->nullable();
            $table->datetime('accepted_by_hotel_at')->nullable();
            $table->float('commission')->nullable();
            $table->float('rebate')->nullable();
            $table->float('additional_charge_per_adult')->nullable();
            $table->float('tax_rate')->nullable();
            $table->integer('min_age_to_check_in')->nullable();
            $table->float('additional_fees')->nullable();
            $table->string('additional_fees_units')->nullable();
            $table->text('deposit_policy')->nullable();
            $table->text('cancellation_policy')->nullable();
            $table->integer('cancellation_policy_days')->nullable();
            $table->string('cancellation_policy_file')->nullable();
            $table->text('notes')->nullable();
            $table->text('attachments')->nullable();
            $table->text('questions')->nullable();
            $table->text('meeting_spaces')->nullable();
            $table->text('food_and_beverage')->nullable();
            $table->date('cutoff_date')->nullable();
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
        Schema::drop('contracts');
    }
}
