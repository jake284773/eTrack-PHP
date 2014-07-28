<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCriteriaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('criteria', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('criteria_id', 3);
			$table->string('description');
			$table->unsignedInteger('unit_id');
			$table->unsignedInteger('learning_outcome_id');
			$table->timestamps();

			$table->unique(array(
				'unit_id', 'criteria_id',
				'learning_outcome_id'
			));

			$table->foreign('unit_id')
				->references('id')->on('units')
				->onDelete('cascade');

			$table->foreign('learning_outcome_id')
				->references('id')->on('learning_outcomes')
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
		Schema::drop('criteria');
	}

}
