<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsAttritionAcceptableToProposalRequestsTable extends Migration
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
            $table->boolean('is_attrition_acceptable')->default(false)->after('is_meeting_space_required');
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
            $table->dropColumn('is_attrition_acceptable');
        });
    }
}
