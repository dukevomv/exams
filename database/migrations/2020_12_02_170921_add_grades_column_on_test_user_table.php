<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGradesColumnOnTestUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('test_user', function (Blueprint $table) {
            $table->json('grades')->nullable();
            $table->datetime('graded_at')->nullable();
            $table->integer('graded_by')->unsigned()->nullable();
            $table->foreign('graded_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('test_user', function (Blueprint $table) {
            $table->dropColumn('grades');
            $table->dropColumn('graded_at');
            $table->dropForeign(['graded_by']);
            $table->dropColumn('graded_by');
        });
    }
}
