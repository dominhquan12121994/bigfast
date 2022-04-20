<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderFeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_fee', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('shop_id');
            $table->unsignedBigInteger('order_id');
            $table->char('fee_type', 50);
            $table->integer('date');
            $table->integer('value');
        });

        DB::statement('ALTER TABLE `order_fee` ADD INDEX `order_fee_date_index` (`shop_id`, `date` DESC)');
        DB::statement('ALTER TABLE `order_fee` ADD UNIQUE `order_fee_unique_index`(`order_id`, `fee_type`)');

        Schema::table('order_fee', function($table) {
            $table->foreign('order_id')->references('id')->on('orders');
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
        Schema::dropIfExists('order_fee');
    }
}
