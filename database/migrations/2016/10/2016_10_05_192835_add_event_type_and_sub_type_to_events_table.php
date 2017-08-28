<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEventTypeAndSubTypeToEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function(Blueprint $table)
        {
            $table->integer('event_type_id')->unsigned()->index()->nullable()->after('event_group_id');
            $table->integer('event_sub_type_id')->unsigned()->index()->nullable()->after('event_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function(Blueprint $table)
        {
            $table->dropColumn('event_type_id');
        });

        Schema::table('events', function(Blueprint $table)
        {
            $table->dropColumn('event_sub_type_id');
        });
    }
}
