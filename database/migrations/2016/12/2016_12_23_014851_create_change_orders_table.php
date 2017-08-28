<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChangeOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('change_orders', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('contract_id')->unsigned()->index();
            $table->string('initiated_by_party');
            $table->integer('initiated_by_user');
            $table->integer('declined_by_user')->nullable();
            $table->datetime('declined_at')->nullable();
            $table->text('declined_because')->nullable();
            $table->integer('accepted_by_user')->nullable();
            $table->datetime('accepted_at')->nullable();
            $table->text('change_data');
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
        Schema::drop('change_orders');
    }
}
