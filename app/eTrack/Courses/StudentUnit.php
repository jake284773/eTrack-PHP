<?php namespace eTrack\Courses;

use eTrack\Core\Entity;
use Illuminate\Database\Eloquent\Builder;

class StudentUnit extends Entity {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'student_unit';

    /**
     * The attributes that can be mass-assigned.
     *
     * @var array
     */
    protected $fillable = ['student_user_id', 'unit_id', 'grade'];

    /**
     * Set the keys for a save update query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery(Builder $query)
    {
        $query->where('student_user_id', '=', $this->attributes['student_user_id']);
        $query->where('unit_id', '=', $this->attributes['unit_id']);

        return $query;
    }

    public function unit()
    {
        return $this->belongsTo('eTrack\Courses\Unit');
    }

    public function student()
    {
        return $this->belongsTo('eTrack\Accounts\Student', 'student_user_id');
    }

    public function scopeAllForCourse($query, $courseId)
    {
        return $query
            ->select('student_unit.*')
            ->leftJoin('course_unit', 'student_unit.unit_id', '=', 'course_unit.unit_id')
            ->where('course_unit.course_id', '=', $courseId);
    }

} 