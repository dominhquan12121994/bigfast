<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersQueueTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_queues', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('status');
            $table->unsignedInteger('shop_id');
            $table->unsignedInteger('created_date');
            $table->string('receiver_name', 255);
            $table->string('receiver_phone', 255);
            $table->string('receiver_address', 255);
            $table->unsignedInteger('cod');
            $table->string('client_code', 255)->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();
        });

        Schema::table('order_queues', function($table) {
            $table->foreign('shop_id')->references('id')->on('order_shops');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_queues');
    }
}
