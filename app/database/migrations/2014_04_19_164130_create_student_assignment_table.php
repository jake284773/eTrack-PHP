<?php

/*
 * This file is part of the eTrack web application.
 *
 * (c) City College Plymouth
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStudentAssignmentTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_assignment', function(Blueprint $table) {
            $table->string("assignment_id", 15);
            $table->string("student_user_id", 25);
            $table->dateTime("special_deadline")->nullable();
            $table->dateTime("submission_date")->nullable();

            $table->foreign("assignment_id")
                ->references("id")
                ->on("assignment");
            $table->foreign("student_user_id")
                ->references("id")
                ->on("user");

            $table->primary(array("assignment_id", "student_user_id"));
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('student_assignment');
    }

}
