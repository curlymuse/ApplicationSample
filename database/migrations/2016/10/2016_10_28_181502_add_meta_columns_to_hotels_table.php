<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMetaColumnsToHotelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hotels', function(Blueprint $table)
        {
            $table->string('address')->nullable()->after('name');
            $table->string('city')->nullable()->after('address');
            $table->string('zip')->nullable()->after('city');
            $table->string('state')->nullable()->after('zip');
            $table->string('country')->nullable()->after('state');
            $table->text('description')->nullable();
            $table->integer('sleeping_rooms')->default(0)->after('description');
            $table->integer('meeting_rooms')->default(0)->after('description');
            $table->integer('largest_meeting_room_sq_ft')->default(0)->after('description');
            $table->integer('total_meeting_room_sq_ft')->default(0)->after('description');
            $table->float('rate_min')->default(0.00)->after('description');
            $table->float('rate_max')->default(0.00)->after('description');
            $table->float('travelocity_stars')->default(0.00)->after('description');
            $table->integer('travelocity_rating')->nullable()->after('description');
            $table->integer('travelocity_reviews')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hotels', function(Blueprint $table)
        {
            $table->dropColumn('address');
        });

        Schema::table('hotels', function(Blueprint $table)
        {
            $table->dropColumn('city');
        });

        Schema::table('hotels', function(Blueprint $table)
        {
            $table->dropColumn('zip');
        });

        Schema::table('hotels', function(Blueprint $table)
        {
            $table->dropColumn('state');
        });

        Schema::table('hotels', function(Blueprint $table)
        {
            $table->dropColumn('country');
        });

        Schema::table('hotels', function(Blueprint $table)
        {
            $table->dropColumn('description');
        });

        Schema::table('hotels', function(Blueprint $table)
        {
            $table->dropColumn('sleeping_rooms');
        });

        Schema::table('hotels', function(Blueprint $table)
        {
            $table->dropColumn('meeting_rooms');
        });

        Schema::table('hotels', function(Blueprint $table)
        {
            $table->dropColumn('largest_meeting_room_sq_ft');
        });

        Schema::table('hotels', function(Blueprint $table)
        {
            $table->dropColumn('total_meeting_room_sq_ft');
        });

        Schema::table('hotels', function(Blueprint $table)
        {
            $table->dropColumn('rate_min');
        });

        Schema::table('hotels', function(Blueprint $table)
        {
            $table->dropColumn('rate_max');
        });

        Schema::table('hotels', function(Blueprint $table)
        {
            $table->dropColumn('travelocity_stars');
        });

        Schema::table('hotels', function(Blueprint $table)
        {
            $table->dropColumn('travelocity_rating');
        });

        Schema::table('hotels', function(Blueprint $table)
        {
            $table->dropColumn('travelocity_reviews');
        });
    }
}
