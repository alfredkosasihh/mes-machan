<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyStandardWorkingHours extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('day_performance_statistics', function (Blueprint $table) {
            $table->float('standard_working_hours')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('day_performance_statistics', function (Blueprint $table) {
            $table->unsignedInteger('standard_working_hours')->change();
        });
    }
}
