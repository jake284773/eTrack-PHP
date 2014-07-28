<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLearningOutcomesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('learning_outcomes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('number');
			$table->string('description');
			$table->unsignedInteger('unit_id');
			$table->timestamps();

			 $table->unique(array('number', 'unit_id'));

			$table->foreign('unit_id')
				->references('id')->on('units')
				->onDelete('cascade');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('learning_outcomes');
	}

}
