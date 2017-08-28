<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTitleToLicenseeTermsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('licensee_terms', function(Blueprint $table)
        {
            $table->string('title')->default('')->after('licensee_term_group_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('licensee_terms', function(Blueprint $table)
        {
            $table->dropColumn('title');
        });
    }
}
