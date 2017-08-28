<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMetaFieldsToPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('guest_room_night', function(Blueprint $table)
        {
            $table->renameColumn('room_night_id', 'reservation_id');
        });
        Schema::table('guest_room_night', function(Blueprint $table)
        {
            $table->boolean('is_primary')->default(false)->after('reservation_id');
            $table->string('payment_type')->nullable()->after('is_primary');
            $table->text('notes_to_hotel')->nullable()->after('payment_type');
            $table->text('notes_internal')->nullable()->after('notes_to_hotel');
            $table->text('special_requests')->nullable()->after('notes_internal');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('guest_room_night', function(Blueprint $table)
        {
            $table->renameColumn('reservation_id', 'room_night_id');
        });
        Schema::table('guest_room_night', function(Blueprint $table)
        {
            $table->dropColumn('is_primary');
            $table->dropColumn('payment_type');
            $table->dropColumn('notes_to_hotel');
            $table->dropColumn('notes_internal');
            $table->dropColumn('special_requests');
        });
    }
}
