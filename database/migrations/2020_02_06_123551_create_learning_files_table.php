<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLearningFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('learning_files', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->smallInteger('topic')->unique();
            $table->text('guide');
            $table->text('testfile');
            $table->text('supplement');
            $table->text('other')->default('');
            $table->text('desc')->default('-');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('learning_files');
    }
}
