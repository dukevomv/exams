<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsInTestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->integer('started_by_user')->unsigned()->nullable()->after('started_at');
            $table->foreign('started_by_user')->references('id')->on('users')->onDelete('set null');
            $table->integer('finished_by_user')->unsigned()->nullable()->after('finished_at');
            $table->foreign('finished_by_user')->references('id')->on('users')->onDelete('set null');
            $table->integer('graded_by_user')->unsigned()->nullable()->after('graded_at');
            $table->foreign('graded_by_user')->references('id')->on('users')->onDelete('set null');
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
            $table->dropForeign(['started_by_user']);
            $table->dropcolumn('started_by_user');
            $table->dropForeign(['finished_by_user']);
            $table->dropcolumn('finished_by_user');
            $table->dropForeign(['graded_by_user']);
            $table->dropcolumn('graded_by_user');
        });
    }
}
