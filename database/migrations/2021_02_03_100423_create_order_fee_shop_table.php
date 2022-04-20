<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderFeeShopTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_fee_shop', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('shop_id');
            $table->char('fee_type', 50);
            $table->integer('date');
            $table->integer('value');
        });

        DB::statement('ALTER TABLE `order_fee_shop` ADD INDEX `order_fee_shop_date_index` (`shop_id`, `date` DESC)');

        Schema::table('order_fee_shop', function($table) {
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
        Schema::dropIfExists('order_fee_shop');
    }
}
