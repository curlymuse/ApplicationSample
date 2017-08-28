<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDateColumnsToContractsTable extends Migration
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
            $table->date('start_date')->nullable()->after('event_date_range_id');
            $table->date('end_date')->nullable()->after('start_date');
            $table->date('check_in_date')->nullable()->after('end_date');
            $table->date('check_out_date')->nullable()->after('check_in_date');
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
            $table->dropColumn('start_date');
            $table->dropColumn('end_date');
            $table->dropColumn('check_in_date');
            $table->dropColumn('check_out_date');
        });
    }
}
