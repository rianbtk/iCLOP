<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentSubmitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_submits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('userid');
            $table->smallInteger('topic');
            $table->string('validstat');
            $table->string('checkstat')->default('waiting');
            $table->string('checkresult')->default('');
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
        Schema::dropIfExists('student_submits');
    }
}
