<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropDeprecatedReservationColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reservations', function(Blueprint $table)
        {
            $table->dropColumn('address1');
        });
        Schema::table('reservations', function(Blueprint $table)
        {
            $table->dropColumn('address2');
        });
        Schema::table('reservations', function(Blueprint $table)
        {
            $table->dropColumn('room_type');
        });
        Schema::table('reservations', function(Blueprint $table)
        {
            $table->dropColumn('reservation_date');
        });
        Schema::table('reservations', function(Blueprint $table)
        {
            $table->dropColumn('contract_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reservations', function(Blueprint $table)
        {
            $table->integer('contract_id')->unsigned()->index();
            $table->datetime('reservation_date');
            $table->string('room_type')->nullable();
            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
        });
    }
}
