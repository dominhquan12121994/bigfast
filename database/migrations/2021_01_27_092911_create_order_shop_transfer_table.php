<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderShopTransferTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_shop_transfer', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('date');
            $table->unsignedInteger('shop_id');
            $table->bigInteger('value');
            $table->unsignedInteger('user_id');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_shop_transfer');
    }
}
