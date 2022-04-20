<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('status');
            $table->smallInteger('status_detail');
            $table->char('lading_code', 50)->unique();
            $table->char('service_type', 50);
            $table->char('payfee', 50);
            $table->unsignedInteger('shop_id');
            $table->unsignedInteger('sender_id');
            $table->unsignedInteger('refund_id');
            $table->unsignedBigInteger('receiver_id');
            $table->unsignedInteger('transport_fee');
            $table->unsignedInteger('total_fee');
            $table->unsignedInteger('cod');
            $table->unsignedInteger('insurance_value');
            $table->unsignedInteger('weight')->comment('gram');
            $table->unsignedInteger('height')->comment('cm');
            $table->unsignedInteger('width')->comment('cm');
            $table->unsignedInteger('length')->comment('cm');
            $table->unsignedInteger('created_date');
            $table->unsignedInteger('send_success_date')->nullable();
            $table->unsignedInteger('collect_money_date')->nullable();
            $table->unsignedInteger('reconcile_send_date')->nullable();
            $table->unsignedInteger('reconcile_refund_date')->nullable();
            $table->unsignedInteger('last_change_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement('ALTER TABLE `orders` ADD INDEX `orders_shop_id_created_date` (`shop_id`, `status`, `created_date` DESC)');
        DB::statement('ALTER TABLE `orders` ADD INDEX `orders_shop_id_collect_money_date` (`shop_id`, `collect_money_date` DESC)');

        Schema::table('orders', function($table) {
            $table->foreign('shop_id')->references('id')->on('order_shops');
            $table->foreign('sender_id')->references('id')->on('order_shops_address');
            $table->foreign('refund_id')->references('id')->on('order_shops_address');
            $table->foreign('receiver_id')->references('id')->on('order_receivers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
