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

class CreateStudentGroupStudentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('student_group_student', function(Blueprint $table) {
			$table->string("student_group_id", 15);
			$table->string("student_user_id", 25);

			$table->foreign("student_group_id")
				->references("id")
				->on("student_group");
			$table->foreign("student_user_id")
				->references("id")
				->on("user");

			$table->primary(array("student_group_id", "student_user_id"));
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('student_group_student');
	}

}
