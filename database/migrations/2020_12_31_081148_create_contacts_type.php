<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts_type', function (Blueprint $table) {
            $table->id();
            $table->integer('parent_id');
            $table->string('name');
            $table->unsignedInteger('sla')->default('0')->comment('Thời gian xử lý theo phút')->nullable();
            $table->smallInteger('level')->default('0');
            $table->smallInteger('status')->default('0');
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
        Schema::dropIfExists('contacts_type');
    }
}
