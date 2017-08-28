<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAcceptedByLicenseeColumnsToContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contracts', function(Blueprint $table)
        {
            $table->datetime('accepted_by_licensee_at')->nullable()->after('accepted_by_hotel_user');
            $table->integer('accepted_by_licensee_user')->unsigned()->index()->nullable()->after('accepted_by_licensee_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contracts', function(Blueprint $table)
        {
            $table->dropColumn('accepted_by_licensee_at');
        });

        Schema::table('contracts', function(Blueprint $table)
        {
            $table->dropColumn('accepted_by_licensee_user');
        });
    }
}
