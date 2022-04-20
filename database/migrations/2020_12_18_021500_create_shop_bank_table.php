<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopBankTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_shops_bank', function (Blueprint $table) {
            $table->unsignedInteger('id');
            $table->string('bank_name')->default('');
            $table->string('bank_branch')->default('');
            $table->string('stk_name')->default('');
            $table->string('stk')->default('');
            $table->string('cycle_cod');
            $table->string('purpose')->default('');
            $table->string('scale')->default('');
            $table->string('branch')->default('');
            $table->unsignedInteger('cycle_cod_day');
            $table->date('date_reconcile');

            $table->primary('id');
        });

        Schema::table('order_shops_bank', function($table) {
            $table->foreign('id')->references('id')->on('order_shops');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_shops_bank');
    }
}
