<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemNotificationSendTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_notification_send', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('notification_id');
            $table->unsignedInteger('shop_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->boolean('is_read')->default(0);
            $table->unsignedInteger('created_date');
            $table->softDeletes();
        });

        Schema::table('system_notification_send', function($table) {
            $table->foreign('notification_id')->references('id')->on('system_notifications');
            $table->foreign('shop_id')->references('id')->on('order_shops');
            $table->foreign('user_id')->references('id')->on('system_users');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_notification_send');
    }
}
