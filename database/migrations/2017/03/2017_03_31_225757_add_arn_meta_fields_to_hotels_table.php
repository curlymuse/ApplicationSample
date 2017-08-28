<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddArnMetaFieldsToHotelsTable extends Migration
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
            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->integer('floors')->nullable();
            $table->integer('year_built')->nullable();
            $table->integer('year_of_last_renovation')->nullable();
            $table->string('property_phone')->nullable();
            $table->string('property_fax')->nullable();
            $table->string('property_email')->nullable();
            $table->integer('mobil_star_rating')->nullable();
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
            $table->dropColumn('address1');
            $table->dropColumn('address2');
            $table->dropColumn('floors');
            $table->dropColumn('year_built');
            $table->dropColumn('year_of_last_renovation');
            $table->dropColumn('property_phone');
            $table->dropColumn('property_fax');
            $table->dropColumn('property_email');
            $table->dropColumn('mobil_star_rating');
        });
    }
}
