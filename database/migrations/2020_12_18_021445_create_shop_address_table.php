<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_shops_address', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('shop_id');
            $table->unsignedInteger('p_id');
            $table->unsignedInteger('d_id');
            $table->unsignedInteger('w_id');
            $table->enum('type', ['send', 'refund'])->default('send');
            $table->string('name')->default('');
            $table->string('phone')->default('');
            $table->string('address')->default('');
            $table->boolean('default')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('order_shops_address', function($table) {
            $table->foreign('shop_id')->references('id')->on('order_shops');
            $table->foreign('p_id')->references('id')->on('zone_provinces');
            $table->foreign('d_id')->references('id')->on('zone_districts');
            $table->foreign('w_id')->references('id')->on('zone_wards');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_shops_address');
    }
}
