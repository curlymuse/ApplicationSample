<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDescriptionAndOtherMetaColumnsToProposalRequestsTable extends Migration
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
            $table->integer('anticipated_attendance')->nullable()->after('rebate');
            $table->text('description')->nullable()->after('anticipated_attendance');
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
            $table->dropColumn('anticipated_attendance');
        });

        Schema::table('proposal_requests', function(Blueprint $table)
        {
            $table->dropColumn('description');
        });
    }
}
