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

class CreateAssignmentCriteriaTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assignment_criteria', function(Blueprint $table) {
            $table->string("assignment_id", 15);
            $table->string("criteria_id", 4);
            $table->string("criteria_unit_id", 12);

            $table->foreign("assignment_id")
                ->references("id")
                ->on("assignment");
            $table->foreign("criteria_id")
                ->references("id")
                ->on("criteria");
            $table->foreign("criteria_unit_id", "assignment_criteria_unit_id_foreign")
                ->references("id")
                ->on("unit");

            $table->primary(array("assignment_id", "criteria_id", "criteria_unit_id"))
                ->index("assignment_criteria_primary");
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('assignment_criteria');
    }

}
