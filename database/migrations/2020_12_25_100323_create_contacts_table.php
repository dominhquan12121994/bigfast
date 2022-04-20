<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->char('lading_code', 50);
            $table->unsignedInteger('shop_id');
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('contacts_type_id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('assign_id')->nullable();
            $table->enum('type', ['admin', 'shop'])->default('admin');
            $table->string('detail');
            $table->smallInteger('status');
            $table->text('file_path')->nullable();
            $table->text('last_update')->nullable();
            $table->date('created_date')->nullable();
            $table->boolean('expired')->default(0);
            $table->datetime('expired_at')->nullable();
            $table->datetime('done_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}
