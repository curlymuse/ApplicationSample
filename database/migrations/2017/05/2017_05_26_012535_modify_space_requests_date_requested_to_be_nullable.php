<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifySpaceRequestsDateRequestedToBeNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('space_requests', function (Blueprint $table) {
            $table->date('date_requested')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('space_requests', function (Blueprint $table) {
            $table->date('date_requested')->nullable(false)->change();
        });
    }
}
