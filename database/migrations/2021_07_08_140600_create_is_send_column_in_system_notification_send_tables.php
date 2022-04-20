<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIsSendColumnInSystemNotificationSendTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('system_notification_send', function (Blueprint $table) {
            $table->integer('created_time')->default(time());
            $table->tinyInteger('is_send')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('system_notification_send', function (Blueprint $table) {
            $table->dropColumn('created_time');
            $table->dropColumn('is_send');
        });
    }
}
