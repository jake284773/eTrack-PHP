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

class CreateUnitTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unit', function(Blueprint $table) {
            $table->string("id", 12);
            $table->integer("number");
            $table->string("name", 100);
            $table->integer("credit_value");
            $table->integer("glh");
            $table->integer("level");
            $table->decimal('subject_sector_id', 3, 1);

            $table->foreign("subject_sector_id")
                ->references("id")
                ->on("subject_sector");

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
        Schema::drop('unit');
    }

}
