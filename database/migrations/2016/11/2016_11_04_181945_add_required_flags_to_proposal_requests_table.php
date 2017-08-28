<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRequiredFlagsToProposalRequestsTable extends Migration
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
            $table->boolean('is_meeting_space_required')->default(true)->after('is_visible_to_planner');
            $table->boolean('is_food_and_beverage_required')->default(true)->after('is_visible_to_planner');
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
            $table->dropColumn('is_meeting_space_required');
        });

        Schema::table('proposal_requests', function(Blueprint $table)
        {
            $table->dropColumn('is_food_and_beverage_required');
        });
    }
}
