<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZoneProvincesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zone_provinces', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('zone', ['bac', 'trung', 'nam'])->default('bac');
            $table->integer('code')->unique()->unsigned();
            $table->char('alias', 50)->default('');
            $table->string('short_name');
            $table->string('name');
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
        Schema::table('zone_provinces', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
