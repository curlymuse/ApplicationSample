<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveAcceptByLicenseeFromContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contracts', function(Blueprint $table) {
            $table->dropColumn('accepted_by_licensee_at');
        });
        Schema::table('contracts', function(Blueprint $table) {
            $table->dropColumn('accepted_by_licensee_user');
        });
        Schema::table('contracts', function(Blueprint $table) {
            $table->dropColumn('accepted_by_licensee_signature');
        });
        Schema::table('contracts', function(Blueprint $table) {
            $table->dropColumn('declined_by_licensee_at');
        });
        Schema::table('contracts', function(Blueprint $table) {
            $table->dropColumn('declined_by_licensee_user');
        });
        Schema::table('contracts', function(Blueprint $table) {
            $table->dropColumn('declined_by_licensee_because');
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
            $table->datetime('declined_by_licensee_at')->nullable()->after('declined_by_hotel_because');
            $table->integer('declined_by_licensee_user')->nullable()->after('declined_by_licensee_at');
            $table->string('declined_by_licensee_because')->nullable()->after('declined_by_licensee_user');
            $table->datetime('accepted_by_licensee_at')->nullable()->after('accepted_by_hotel_signature');
            $table->integer('accepted_by_licensee_user')->nullable()->after('accepted_by_licensee_at');
            $table->string('accepted_by_licensee_signature')->nullable()->after('accepted_by_licensee_user');
        });
    }
}
