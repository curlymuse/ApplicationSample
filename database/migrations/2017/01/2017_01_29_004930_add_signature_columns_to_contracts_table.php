<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSignatureColumnsToContractsTable extends Migration
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
            $table->string('accepted_by_licensee_signature')->nullable()->after('accepted_by_licensee_user');
            $table->string('accepted_by_hotel_signature')->nullable()->after('accepted_by_hotel_user');
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
            $table->dropColumn('accepted_by_licensee_signature');
            $table->dropColumn('accepted_by_hotel_signature');
        });
    }
}
