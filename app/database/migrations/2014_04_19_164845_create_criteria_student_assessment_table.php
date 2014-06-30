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

class CreateCriteriaStudentAssessmentTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('criteria_student_assessment', function(Blueprint $table) {
            $table->string("student_assignment_assignment_id", 15);
            $table->string("student_assignment_student_user_id", 25);
            $table->string("criteria_id", 4);
            $table->string("criteria_unit_id", 12);
            $table->string("assessor_user_id", 25);
            $table->string("moderator_user_id", 25)->nullable();
            $table->string("assessment_status", 3);
            $table->timestamps();

            $table->foreign(["student_assignment_assignment_id",
                    "student_assignment_student_user_id"],
                "student_assignment_foreign")
                ->references(["assignment_id", "student_user_id"])
                ->on("student_assignment");
            $table->foreign(["criteria_id", "criteria_unit_id"])
                ->references(["id", "unit_id"])
                ->on("criteria");
            $table->foreign("assessor_user_id")
                ->references("id")
                ->on("user");
            $table->foreign("moderator_user_id")
                ->references("id")
                ->on("user");

            $table->primary([
                "student_assignment_assignment_id",
                "student_assignment_student_user_id",
                "criteria_id",
                "criteria_unit_id"
                ])->index("criteria_student_assessment_primary");
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('criteria_student_assessment');
    }

}
