<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderShipAssignedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_ship_assigned', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('user_role', 20);
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('shop_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedInteger('sender_id');
            $table->unsignedInteger('p_id');
            $table->unsignedInteger('d_id');
            $table->unsignedInteger('w_id');
            $table->smallInteger('status');
            $table->smallInteger('status_detail');
            $table->integer('time_assigned');
            $table->integer('time_success')->default(0);
            $table->integer('time_failed')->default(0);
            $table->smallInteger('failed_status')->default(0);
            $table->softDeletes();
        });

        Schema::table('order_ship_assigned', function($table) {
            $table->foreign('order_id')->references('id')->on('orders');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_ship_assigned');
    }
}
