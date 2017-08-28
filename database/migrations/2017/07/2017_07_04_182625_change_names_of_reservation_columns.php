<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNamesOfReservationColumns extends Migration
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
            $table->renameColumn('name', 'guest_name');
        });
        Schema::table('reservations', function(Blueprint $table)
        {
            $table->renameColumn('city', 'guest_city');
        });
        Schema::table('reservations', function(Blueprint $table)
        {
            $table->renameColumn('state', 'guest_state');
        });
        Schema::table('reservations', function(Blueprint $table)
        {
            $table->renameColumn('country', 'guest_country');
        });
        Schema::table('reservations', function(Blueprint $table)
        {
            $table->renameColumn('zip', 'guest_zip');
        });
        Schema::table('reservations', function(Blueprint $table)
        {
            $table->renameColumn('phone', 'guest_phone');
        });
        Schema::table('reservations', function(Blueprint $table)
        {
            $table->renameColumn('special_requests', 'guest_special_requests');
        });
        Schema::table('reservations', function(Blueprint $table)
        {
            $table->renameColumn('hotel_confirmation_number', 'confirmation_number');
        });
        Schema::table('reservations', function(Blueprint $table)
        {
            $table->renameColumn('hotel_cancellation_number', 'cancellation_number');
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
            $table->renameColumn('guest_name', 'name');
        });
        Schema::table('reservations', function(Blueprint $table)
        {
            $table->renameColumn('guest_city', 'city');
        });
        Schema::table('reservations', function(Blueprint $table)
        {
            $table->renameColumn('guest_state', 'state');
        });
        Schema::table('reservations', function(Blueprint $table)
        {
            $table->renameColumn('guest_country', 'country');
        });
        Schema::table('reservations', function(Blueprint $table)
        {
            $table->renameColumn('guest_zip', 'zip');
        });
        Schema::table('reservations', function(Blueprint $table)
        {
            $table->renameColumn('guest_phone', 'phone');
        });
        Schema::table('reservations', function(Blueprint $table)
        {
            $table->renameColumn('guest_special_requests', 'special_requests');
        });
        Schema::table('reservations', function(Blueprint $table)
        {
            $table->renameColumn('confirmation_number', 'hotel_confirmation_number');
        });
        Schema::table('reservations', function(Blueprint $table)
        {
            $table->renameColumn('cancellation_number', 'hotel_cancellation_number');
        });
    }
}
