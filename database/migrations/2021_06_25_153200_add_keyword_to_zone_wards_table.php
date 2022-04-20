<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKeywordToZoneWardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('zone_wards', function (Blueprint $table) {
            $table->string('keyword')->nullable();
        });
        Schema::table('zone_wards', function (Blueprint $table) {
            DB::statement('ALTER TABLE zone_wards ADD FULLTEXT (keyword)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('zone_wards', function (Blueprint $table) {
            $table->dropColumn('keyword');
        });
    }
}
