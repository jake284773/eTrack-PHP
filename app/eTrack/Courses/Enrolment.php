<?php namespace eTrack\Courses;

use eTrack\Accounts\Student;
use eTrack\Core\Entity;

/**
 * Enrolment model
 *
 * @property-read Course $course
 * @property-read Student $student
 * @property string $final_grade
 * @property string $predicted_grade
 * @property string $target_grade
 * @property integer $final_ucas_tariff_score
 * @property integer $predicted_ucas_tariff_score
 * @property string $course_id
 * @property string $student_user_id
 * @method static Enrolment compositeKey($courseId, $studentUserId)
 */
class Enrolment extends Entity
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'course_student';

    public function scopeCompositeKey($query, $courseId, $studentUserId)
    {
        return $query
            ->where('course_id', $courseId)
            ->where('student_user_id', $studentUserId);
    }

    public function course()
    {
        return $this->belongsTo('eTrack\Courses\Course');
    }

    public function student()
    {
        return $this->hasOne('eTrack\Accounts\Student', 'student_user_id');
    }

}