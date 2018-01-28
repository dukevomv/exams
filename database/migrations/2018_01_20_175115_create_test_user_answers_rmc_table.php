<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestUserAnswersRmcTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_user_answers_rmc', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('test_user_id')->unsigned();
            $table->foreign('test_user_id')->references('id')->on('test_user')->onDelete('cascade');
            $table->integer('answer_id')->unsigned();
            $table->foreign('answer_id')->references('id')->on('answers_rmc')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('test_user_answers_rmc');
    }
}
