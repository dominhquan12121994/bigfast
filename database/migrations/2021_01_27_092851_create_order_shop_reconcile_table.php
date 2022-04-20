<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderShopReconcileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_shop_reconcile', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('date');
            $table->unsignedInteger('shop_id');
            $table->bigInteger('total_cod')->default(0);
            $table->bigInteger('money_indemnify')->default(0);
            $table->bigInteger('fee_transport')->default(0);
            $table->bigInteger('fee_insurance')->default(0);
            $table->bigInteger('fee_cod')->default(0);
            $table->bigInteger('fee_refund')->default(0);
            $table->bigInteger('fee_store')->default(0);
            $table->bigInteger('fee_change_info')->default(0);
            $table->bigInteger('fee_transfer')->default(0);
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
        Schema::dropIfExists('order_shop_reconcile');
    }
}
