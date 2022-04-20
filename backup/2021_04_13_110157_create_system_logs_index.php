<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemLogsIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mongodb')->table('system_logs', function (Blueprint $table) {
            $table->index([
                "user_id" => -1,
                "user_type" => "text",
                "method" => "text",
                "uri" => "text"
            ],
            'recipe_full_text',
            null,
            [
                'sparse' => true,
                'name' => 'recipe_full_text'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mongodb')->table('system_logs', function (Blueprint $table) {
            $table->dropIndex(['recipe_full_text']);
        });
    }
}
