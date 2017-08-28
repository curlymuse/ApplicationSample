<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnAndValueFieldsToChangeOrdersTable extends Migration
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
            $table->string('change_key')->nullable()->after('accepted_at');
            $table->text('change_display')->nullable()->after('change_key');
            $table->text('original_value')->nullable()->after('change_display');
            $table->text('proposed_value')->nullable()->after('original_value');
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
            $table->dropColumn('change_key');
        });

        Schema::table('change_orders', function(Blueprint $table)
        {
            $table->dropColumn('change_display');
        });

        Schema::table('change_orders', function(Blueprint $table)
        {
            $table->dropColumn('original_value');
        });

        Schema::table('change_orders', function(Blueprint $table)
        {
            $table->dropColumn('proposed_value');
        });
    }
}
