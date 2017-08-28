<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToReservationsTable extends Migration
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
            $table->string('guest_address')->nullable()->after('cancellation_number');
            $table->string('guest_payment_type')->nullable()->after('guest_phone');
            $table->text('guest_notes_to_hotel')->nullable()->after('guest_special_requests');
            $table->text('guest_notes_internal')->nullable()->after('guest_notes_to_hotel');
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
            $table->dropColumn('guedropColumn');
            $table->dropColumn('guest_payment_type');
            $table->dropColumn('guest_notes_to_hotel');
            $table->dropColumn('guest_notes_internal');
        });
    }
}
