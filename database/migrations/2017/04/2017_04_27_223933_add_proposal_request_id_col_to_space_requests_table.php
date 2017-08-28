<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProposalRequestIdColToSpaceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('space_requests', function(Blueprint $table)
        {
            $table->integer('proposal_request_id')->nullable()->unsigned()->index()->after('event_date_range_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('space_requests', function(Blueprint $table)
        {
            $table->dropColumn('proposal_request_id');
        });
    }
}
