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

class CreateStudentGroupTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('student_group', function(Blueprint $table) {
			$table->string("id", 15);
            $table->string("course_id", 15);
            $table->string("tutor_user_id", 25);

            $table->foreign("course_id")
                ->references("id")
                ->on("course");
            $table->foreign("tutor_user_id")
                ->references("id")
                ->on("user");

            $table->primary("id");
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('student_group');
	}

}
