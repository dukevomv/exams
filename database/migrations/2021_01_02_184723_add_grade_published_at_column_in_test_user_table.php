<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGradePublishedAtColumnInTestUserTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('test_user', function (Blueprint $table) {
            $table->datetime('left_at')->nullable();
            $table->datetime('grade_published_at')->nullable();
            $table->string('published_grade')->nullable();
            $table->dropColumn('started_at');
            $table->dropColumn('grade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('test_user', function (Blueprint $table) {
            $table->dropColumn('left_at');
            $table->dropColumn('grade_published_at');
            $table->dropColumn('published_grade');
            $table->integer('grade');
        });
    }
}
