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

class CreateAssignmentTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assignment', function(Blueprint $table) {
            $table->string("id", 15);
            $table->string("unit_id", 12);
            $table->text('brief')->nullable();
            $table->string('status', 20);
            $table->dateTime("available_date");
            $table->dateTime("deadline");
            $table->dateTime("marking_start_date");
            $table->dateTime("marking_deadline");

            $table->foreign('unit_id')
                ->references('id')
                ->on('unit');

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
        Schema::drop('assignment');
    }

}
