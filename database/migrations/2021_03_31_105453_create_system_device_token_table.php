<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemDeviceTokenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_device_token', function (Blueprint $table) {
            $table->id();
            $table->enum('user_type', ['shop', 'admin']);
            $table->unsignedInteger('user_id');
            $table->enum('device_type', ['web', 'app']);
            $table->text('device_token');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement('ALTER TABLE `system_device_token` ADD INDEX `system_device_token_index` (`user_type`, `device_type`, `user_id` DESC)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('system_device_token');
    }
}
