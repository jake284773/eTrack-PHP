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

class CreateCourseTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('course', function(Blueprint $table) {
            $table->string('id', 15);
            $table->string('name', 100);
            $table->integer('level');
            $table->string('type', 100);
            $table->string('pathway', 100)->nullable();
            $table->decimal('subject_sector_id', 3, 1);
            $table->string('faculty_id', 15);
            $table->string('course_organiser_user_id', 25);

            $table->foreign('subject_sector_id')
                ->references('id')
                ->on('subject_sector');
            $table->foreign('faculty_id')
                ->references('id')
                ->on('faculty');
            $table->foreign('course_organiser_user_id')
                ->references('id')
                ->on('user');

            $table->primary('id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('course');
	}

}
