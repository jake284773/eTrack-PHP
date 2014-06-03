<?php

/**
 * Assignment submission model
 *
 * @property-read Assignment $assignment
 * @property-read Student $student
 * @property DateTime $special_deadline
 * @property DateTime $submission_date
 *
 */
class AssignmentSubmission extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'student_assignment';

    /**
     * Retrieve a assignment submission record with the composite primary key.
     *
     * This is used instead of the standard find method as it doesn't support
     * the use of composite primary keys.
     *
     * @param $query
     * @param string $assignmentId The assignment ID
     * @param string $studentUserId The student's user ID
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompositeKey($query, $assignmentId, $studentUserId)
    {
        return $query
            ->where('assignment_id', $assignmentId)
            ->where('student_user_id', $studentUserId);
    }

    public function student()
    {
        return $this->hasMany('Student', 'id', 'student_user_id');
    }

}