<?php namespace eTrack\Models;

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
        return $this->belongsTo('eTrack\Models\Entities\Course');
    }

    public function students()
    {
        return $this->belongsToMany('User',
            'student_group_student', null, 'student_user_id');
    }

    public function tutor()
    {
        return $this->belongsTo('eTrack\Models\Entities\User', 'tutor_user_id');
    }
}