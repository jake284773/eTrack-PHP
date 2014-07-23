<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCourseTutorTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_tutor', function(Blueprint $table) {
            $table->string("course_id", 15);
            $table->string("tutor_user_id", 25);

            $table->foreign("course_id")
                ->references("id")
                ->on("course");
            $table->foreign("tutor_user_id")
                ->references("id")
                ->on("user");

            $table->primary(["course_id", "tutor_user_id"]);
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('course_tutor');
    }

}
