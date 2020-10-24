<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAnswersColumnOnTestUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('test_user', function (Blueprint $table) {
            $table->json('answers_draft')->nullable();
            $table->json('answers')->nullable();
            $table->datetime('answered_draft_at')->nullable();
            $table->datetime('answered_at')->nullable();
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
            $table->dropColumn('answers_draft');
            $table->dropColumn('answers');
            $table->dropColumn('answered_draft_at');
            $table->dropColumn('answered_at');
        });
    }
}
