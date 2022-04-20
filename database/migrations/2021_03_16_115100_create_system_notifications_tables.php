<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemNotificationsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('sender_id');
            $table->text('link')->nullable();
            $table->unsignedInteger('receiver_quantity');
            $table->unsignedInteger('created_date');
            $table->text('selected_purpose')->nullable();
            $table->text('selected_branch')->nullable();
            $table->text('selected_scale')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('system_notifications', function($table) {
            $table->foreign('sender_id')->references('id')->on('system_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_notifications');
    }
}
