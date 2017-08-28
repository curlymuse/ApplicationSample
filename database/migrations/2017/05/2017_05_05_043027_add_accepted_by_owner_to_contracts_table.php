<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAcceptedByOwnerToContractsTable extends Migration
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
            $table->datetime('declined_by_owner_at')->nullable()->after('declined_by_hotel_because');
            $table->integer('declined_by_owner_user')->nullable()->after('declined_by_owner_at');
            $table->string('declined_by_owner_because')->nullable()->after('declined_by_owner_user');
            $table->datetime('accepted_by_owner_at')->nullable()->after('accepted_by_hotel_signature');
            $table->integer('accepted_by_owner_user')->nullable()->after('accepted_by_owner_at');
            $table->string('accepted_by_owner_signature')->nullable()->after('accepted_by_owner_user');
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
            $table->dropColumn('accepted_by_owner_at');
            $table->dropColumn('accepted_by_owner_user');
            $table->dropColumn('accepted_by_owner_signature');
            $table->dropColumn('declined_by_licensee_at');
            $table->dropColumn('declined_by_licensee_user');
            $table->dropColumn('declined_by_licensee_because');
        });
    }
}
