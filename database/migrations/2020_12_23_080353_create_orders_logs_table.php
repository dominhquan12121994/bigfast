<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('user_type', ['user', 'shop'])->default('user');
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('order_id');
            $table->char('log_type', 50);
            $table->smallInteger('status');
            $table->smallInteger('status_detail');
            $table->text('note1')->nullable();
            $table->text('note2')->nullable();
            $table->longText('logs');
            $table->dateTime('timer', $precision = 0);
        });

        Schema::table('order_logs', function($table) {
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
        Schema::dropIfExists('order_logs');
    }
}
