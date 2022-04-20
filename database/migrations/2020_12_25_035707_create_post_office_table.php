<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostOfficeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_post_offices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->unsignedInteger('p_id');
            $table->unsignedInteger('d_id');
            $table->unsignedInteger('w_id');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('system_post_offices', function($table) {
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
        Schema::dropIfExists('system_post_offices');
    }
}
