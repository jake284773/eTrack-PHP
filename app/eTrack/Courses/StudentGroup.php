<?php namespace eTrack\Courses;

use eTrack\Accounts\Student;
use eTrack\Accounts\User;
use eTrack\Core\Entity;

/**
 * Student group model
 *
 * @property string $id
 * @property-read Course $course
 * @property-read User $tutor
 * @property \Illuminate\Database\Eloquent\Collection|Student[] $students
 * @property string $course_id
 * @property string $tutor_user_id
 */
class StudentGroup extends Entity
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'student_group';

    public function course()
    {
        return $this->belongsTo('eTrack\Courses\Course');
    }

    public function students()
    {
        return $this->belongsToMany('eTrack\Accounts\Student',
            'student_group_student', null, 'student_user_id');
    }

    public function tutor()
    {
        return $this->belongsTo('eTrack\Accounts\User', 'tutor_user_id');
    }
}