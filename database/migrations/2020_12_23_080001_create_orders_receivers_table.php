<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersReceiversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_receivers', function (Blueprint $table) {
            $table->id();
            $table->char('phone', 20);
            $table->string('name', 255);
            $table->unsignedInteger('p_id');
            $table->unsignedInteger('d_id');
            $table->unsignedInteger('w_id');
            $table->text('address');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('order_receivers', function($table) {
            $table->foreign('p_id')->references('id')->on('zone_provinces');
            $table->foreign('d_id')->references('id')->on('zone_districts');
            $table->foreign('w_id')->references('id')->on('zone_wards');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_receivers');
    }
}
