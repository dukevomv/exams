<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTaskDescriptionToHoldLargerContent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $mainCol = 'description';
        $middleCol = 'description_2';
        Schema::table('tasks', function (Blueprint $table) use ($middleCol) {
            $table->text($middleCol);
        });
        
        DB::table('tasks')->update([$middleCol => DB::raw("`".$mainCol."`")]);
        
        Schema::table('tasks', function (Blueprint $table) use ($mainCol) {
            $table->dropColumn($mainCol);
        });
        
        Schema::table('tasks', function (Blueprint $table) use ($mainCol) {
            $table->text($mainCol);
        });
        
        DB::table('tasks')->update([$mainCol => DB::raw("`".$middleCol."`")]);
        
        Schema::table('tasks', function (Blueprint $table) use ($middleCol) {
            $table->dropColumn($middleCol);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
        $mainCol = 'description';
        $middleCol = 'description_2';
        
        Schema::table('tasks', function (Blueprint $table) use ($middleCol) {
            $table->text($middleCol);
        });
        
        DB::table('tasks')->update([$middleCol => DB::raw("`".$mainCol."`")]);
        
        Schema::table('tasks', function (Blueprint $table) use ($mainCol) {
            $table->dropColumn($mainCol);
        });
        
        Schema::table('tasks', function (Blueprint $table) use ($mainCol) {
            $table->string($mainCol);
        });
        
        DB::table('tasks')->update([$mainCol => DB::raw("`".$middleCol."`")]);
        
        Schema::table('tasks', function (Blueprint $table) use ($middleCol) {
            $table->dropColumn($middleCol);
        });
    }
}
