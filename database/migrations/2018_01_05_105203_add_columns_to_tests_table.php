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
            $table->string('description')->nullable()->after('name');
            $table->string('status')->default('draft')->after('description');
            $table->integer('duration')->nullable()->after('status');
            $table->datetime('scheduled_at')->nullable()->after('duration');
            $table->datetime('started_at')->nullable()->after('scheduled_at');
            $table->datetime('finished_at')->nullable()->after('started_at');
            $table->datetime('graded_at')->nullable()->after('finished_at');
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
            $table->dropColumn('description');
            $table->dropColumn('status');
            $table->dropColumn('duration');
            $table->dropColumn('scheduled_at');
            $table->dropColumn('started_at');
            $table->dropColumn('finished_at');
            $table->dropColumn('graded_at');
        });
    }
}
