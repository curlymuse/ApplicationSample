<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditionalMetaColumnsToLicenseesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('licensees', function(Blueprint $table)
        {
            $table->string('fax')->nullable()->after('is_suspended');
            $table->string('phone')->nullable()->after('is_suspended');
            $table->string('country_code', 3)->nullable()->after('is_suspended');
            $table->string('region')->nullable()->after('is_suspended');
            $table->string('city')->nullable()->after('is_suspended');
            $table->string('address')->nullable()->after('is_suspended');
            $table->string('org_type')->nullable()->after('is_suspended');
            $table->string('dba')->nullable()->after('is_suspended');
            $table->string('legal_name')->nullable()->after('is_suspended');
            $table->string('logo')->nullable()->after('is_suspended');
            $table->string('email_banner')->nullable()->after('logo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('licensees', function(Blueprint $table)
        {
            $table->dropColumn('fax');
        });

        Schema::table('licensees', function(Blueprint $table)
        {
            $table->dropColumn('phone');
        });

        Schema::table('licensees', function(Blueprint $table)
        {
            $table->dropColumn('country_code');
        });

        Schema::table('licensees', function(Blueprint $table)
        {
            $table->dropColumn('region');
        });

        Schema::table('licensees', function(Blueprint $table)
        {
            $table->dropColumn('city');
        });

        Schema::table('licensees', function(Blueprint $table)
        {
            $table->dropColumn('address');
        });

        Schema::table('licensees', function(Blueprint $table)
        {
            $table->dropColumn('org_type');
        });

        Schema::table('licensees', function(Blueprint $table)
        {
            $table->dropColumn('dba');
        });

        Schema::table('licensees', function(Blueprint $table)
        {
            $table->dropColumn('legal_name');
        });

        Schema::table('licensees', function(Blueprint $table)
        {
            $table->dropColumn('logo');
        });

        Schema::table('licensees', function(Blueprint $table)
        {
            $table->dropColumn('email_banner');
        });
    }
}
