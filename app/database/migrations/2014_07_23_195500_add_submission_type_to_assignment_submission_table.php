<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddSubmissionTypeToAssignmentSubmissionTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_assignment', function($table)
        {
            $table->string('type', 100);
            $table->string('status', 100);
            $table->string('grade', 100)->nullable();

            $table->dropPrimary(['assignment_id', 'student_user_id']);
            $table->primary(['assignment_id', 'student_user_id',
                'submission_date'], 'PRIMARY');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_assignment', function($table)
        {
            $table->dropColumn(['type', 'status', 'grade']);

            $table->dropPrimary(['assignment_id', 'student_user_id',
                'submission_date']);
            $table->primary(['assignment_id', 'student_user_id'], 'PRIMARY');
        });
    }

}
