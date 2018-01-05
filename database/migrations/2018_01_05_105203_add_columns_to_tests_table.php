<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->tinyInteger('published')->default(0)->after('name');
            $table->datetime('scheduled_at')->nullable()->after('published');
            $table->integer('duration')->nullable()->after('scheduled_at');
            $table->string('description')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->dropColumn('scheduled_at');
            $table->dropColumn('duration');
            $table->dropColumn('description');
        });
    }
}
