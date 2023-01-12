<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAutocompleteInAnswersFreeTextTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('answers_free_text', function (Blueprint $table) {
            $table->boolean('autocomplete')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('answers_free_text', function (Blueprint $table) {
            $table->dropColumn('autocomplete');
        });
    }
}
