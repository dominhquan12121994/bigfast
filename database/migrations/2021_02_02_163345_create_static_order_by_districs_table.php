<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaticOrderByDistricsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_reports_by_zone', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('shop_id');
            $table->unsignedInteger('date');
            $table->unsignedInteger('d_id');
            $table->unsignedBigInteger('count');
            $table->foreign('shop_id')->references('id')->on('order_shops');
            $table->foreign('d_id')->references('id')->on('zone_districts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_reports_by_zone');
    }
}
