<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropOrderChangeDataColumnFromChangeOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('change_orders', function(Blueprint $table)
        {
            $table->dropColumn('change_data');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('change_orders', function(Blueprint $table)
        {
            $table->text('change_data')->after('change_value');
        });
    }
}
