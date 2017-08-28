<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropDeclinedByColumnsFromProposalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('proposals', function(Blueprint $table)
        {
            $table->dropColumn('declined_by_hotel_at');
        });

        Schema::table('proposals', function(Blueprint $table)
        {
            $table->dropColumn('declined_by_hotel_user');
        });

        Schema::table('proposals', function(Blueprint $table)
        {
            $table->dropColumn('declined_by_hotel_because');
        });

        Schema::table('proposals', function(Blueprint $table)
        {
            $table->dropColumn('declined_by_licensee_at');
        });

        Schema::table('proposals', function(Blueprint $table)
        {
            $table->dropColumn('declined_by_licensee_user');
        });

        Schema::table('proposals', function(Blueprint $table)
        {
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
        Schema::table('proposals', function(Blueprint $table)
        {
            $table->datetime('declined_by_hotel_at')->nullable()->after('hotel_id');
            $table->integer('declined_by_hotel_user')->nullable()->after('declined_by_hotel_at');
            $table->text('declined_by_hotel_because')->nullable()->after('declined_by_hotel_user');
            $table->datetime('declined_by_licensee_at')->nullable()->after('declined_by_hotel_because');
            $table->integer('declined_by_licensee_user')->nullable()->after('declined_by_licensee_at');
            $table->text('declined_by_licensee_because')->nullable()->after('declined_by_licensee_user');
        });
    }
}
