<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCourseStudentUnitTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_student_unit', function(Blueprint $table) {
            $table->string("course_id", 6);
            $table->string("student_user_id", 25);
            $table->string("unit_id", 12);
            $table->string("grade", 12);

            $table->foreign("course_id")
                ->references("id")
                ->on("course");
            $table->foreign("student_user_id")
                ->references("id")
                ->on("user");
            $table->foreign("unit_id")
                ->references("id")
                ->on("unit");

            $table->primary(["course_id", "student_user_id", "unit_id"]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('course_student_unit');
    }

}
