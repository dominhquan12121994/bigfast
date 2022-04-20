<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersExtrasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_extras', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->text('note1')->nullable();
            $table->text('note2')->nullable();
            $table->string('client_code', 255)->nullable();
            $table->char('receiver_phone', 20);
            $table->string('receiver_name', 255);
            $table->text('receiver_address');
            $table->unsignedInteger('receiver_p_id');
            $table->unsignedInteger('receiver_d_id');
            $table->unsignedInteger('receiver_w_id');
            $table->string('expect_pick', 255);
            $table->string('expect_receiver', 255);
        });

        Schema::table('order_extras', function($table) {
            $table->foreign('id')->references('id')->on('orders');
            $table->foreign('receiver_p_id')->references('id')->on('zone_provinces');
            $table->foreign('receiver_d_id')->references('id')->on('zone_districts');
            $table->foreign('receiver_w_id')->references('id')->on('zone_wards');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_extras');
    }
}
