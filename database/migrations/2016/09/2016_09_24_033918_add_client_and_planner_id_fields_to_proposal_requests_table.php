<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClientAndPlannerIdFieldsToProposalRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('proposal_requests', function (Blueprint $table) {
            $table->boolean('is_visible_to_planner')->default(true)->after('event_id');
            $table->integer('planner_id')->nullable()->unsigned()->index()->after('event_id');
            $table->boolean('is_visible_to_client')->default(true)->after('event_id');
            $table->integer('client_id')->nullable()->unsigned()->index()->after('event_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('proposal_requests', function (Blueprint $table)
        {
            $table->dropColumn('is_visible_to_client');
        });

        Schema::table('proposal_requests', function (Blueprint $table)
        {
            $table->dropColumn('is_visible_to_planner');
        });

        Schema::table('proposal_requests', function (Blueprint $table)
        {
            $table->dropColumn('client_id');
        });

        Schema::table('proposal_requests', function (Blueprint $table)
        {
            $table->dropColumn('planner_id');
        });
    }
}
