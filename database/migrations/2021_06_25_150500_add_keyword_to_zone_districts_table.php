<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKeywordToZoneDistrictsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('zone_districts', function (Blueprint $table) {
            $table->string('keyword')->nullable();
        });
        Schema::table('zone_districts', function (Blueprint $table) {
            DB::statement('ALTER TABLE zone_districts ADD FULLTEXT (keyword)');
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
            $table->dropColumn('keyword');
        });
    }
}
