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

class CreateCourseStudentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('course_student', function(Blueprint $table) {
			$table->string("course_id", 6);
			$table->string("student_user_id", 25);
			$table->string("final_grade", 45)->nullable();
			$table->string("predicted_grade", 45)->nullable();
			$table->string("target_grade", 45)->nullable();
			$table->integer("final_ucas_tariff_score")->nullable();
			$table->integer("predicted_ucas_tariff_score")->nullable();

			$table->foreign("course_id")
				->references("id")
				->on("course");
			$table->foreign("student_user_id")
				->references("id")
				->on("user");

			$table->primary(array("course_id", "student_user_id"));
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('course_student');
	}

}
