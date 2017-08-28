<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSpecificityColumnToProposalRequestsTable extends Migration
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
            $table->string('specificity')->nullable()->after('cutoff_date');
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
            $table->dropColumn('specificity');
        });
    }
}
