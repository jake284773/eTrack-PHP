<?php

/**
 * Enrolment model
 *
 * @property-read Course $course
 * @propety-read St
 * @property string $final_grade
 * @property string $predicted_grade
 * @property string $target_grade
 * @property integer $final_ucas_tariff_score
 * @property integer $predicted_ucas_tariff_score
 *
 */
class Enrolment extends BaseModel {

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
        return $this->belongsTo('Course');
    }

    public function student()
    {
        return $this->hasOne('Student', 'student_user_id');
    }

}