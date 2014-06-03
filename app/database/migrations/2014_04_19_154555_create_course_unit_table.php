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

class CreateCourseUnitTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_unit', function(Blueprint $table) {
            $table->string("course_id", 6);
            $table->string("unit_id", 12);
            $table->integer("unit_number")->nullable();

            $table->foreign("course_id")
                ->references("id")
                ->on("course");
            $table->foreign("unit_id")
                ->references("id")
                ->on("unit");

            $table->primary(array("course_id", "unit_id"));
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('course_unit');
    }

}
