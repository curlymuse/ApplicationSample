<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMetaDataColumnsToProposalRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('proposal_requests', function(Blueprint $table)
        {
            $table->integer('occupancy_per_room_typical')->nullable()->after('is_visible_to_planner');
            $table->integer('occupancy_per_room_max')->nullable()->after('occupancy_per_room_typical');
            $table->integer('room_nights_consumed_per_comp_request')->nullable()->after('occupancy_per_room_max');
            $table->float('commission')->nullable()->after('room_nights_consumed_per_comp_request');
            $table->float('rebate')->nullable()->after('commission');
            $table->string('currency', 3)->nullable()->default('USD');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('proposal_requests', function(Blueprint $table)
        {
            $table->dropColumn('occupancy_per_room_typical');
        });

        Schema::table('proposal_requests', function(Blueprint $table)
        {
            $table->dropColumn('occupancy_per_room_max');
        });

        Schema::table('proposal_requests', function(Blueprint $table)
        {
            $table->dropColumn('room_nights_consumed_per_comp_request');
        });

        Schema::table('proposal_requests', function(Blueprint $table)
        {
            $table->dropColumn('commission');
        });

        Schema::table('proposal_requests', function(Blueprint $table)
        {
            $table->dropColumn('rebate');
        });

        Schema::table('proposal_requests', function(Blueprint $table)
        {
            $table->dropColumn('currency');
        });
    }
}
