<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyOEEperformancesYieldColumnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('o_e_eperformances', function (Blueprint $table) {
            $table->decimal('machine_utilization_rate', 5, 4)->change();
            $table->decimal('performance_rate', 5, 4)->change();
            $table->decimal('yield', 5, 4)->change();
            $table->decimal('OEE', 5, 4)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('o_e_eperformances', function (Blueprint $table) {
            $table->float('machine_utilization_rate')->change();
            $table->float('performance_rate')->change();
            $table->float('yield')->change();
            $table->float('OEE')->change();
        });
    }
}
