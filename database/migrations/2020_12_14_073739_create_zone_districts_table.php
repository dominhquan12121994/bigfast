<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZoneDistrictsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zone_districts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('p_id');
            $table->enum('type', ['noi', 'ngoai', 'huyen'])->default('noi');
            $table->integer('code')->unique()->unsigned();
            $table->char('alias', 50)->default('');
            $table->string('short_name');
            $table->string('name');
            $table->softDeletes();
        });

        Schema::table('zone_districts', function($table) {
            $table->foreign('p_id')->references('id')->on('zone_provinces');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('zone_districts', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
