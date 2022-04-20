<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTracesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_traces', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->smallInteger('status');
            $table->unsignedBigInteger('log_id');
            $table->dateTime('timer', $precision = 0);
            $table->string('note', 255);
        });

        Schema::table('order_traces', function($table) {
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
        Schema::dropIfExists('order_traces');
    }
}
