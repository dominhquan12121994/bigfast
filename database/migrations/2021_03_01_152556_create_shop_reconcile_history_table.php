<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopReconcileHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_shop_reconcile_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('begin_date');
            $table->integer('end_date');
            $table->unsignedInteger('shop_id');
            $table->bigInteger('total_fee')->default(0);
            $table->bigInteger('total_cod')->default(0);
            $table->bigInteger('money_indemnify')->default(0);
            $table->bigInteger('total_du')->default(0);
            $table->bigInteger('user_reconcile');
            $table->timestamps();
        });

        Schema::table('order_shop_reconcile_history', function($table) {
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
        Schema::dropIfExists('order_shop_reconcile_history');
    }
}
