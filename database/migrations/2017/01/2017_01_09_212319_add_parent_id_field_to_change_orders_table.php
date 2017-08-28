<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParentIdFieldToChangeOrdersTable extends Migration
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
            $table->integer('parent_id')->unsigned()->index()->nullable()->after('contract_id');
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
            $table->dropColumn('parent_id');
        });
    }
}
