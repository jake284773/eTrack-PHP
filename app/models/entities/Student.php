<?php namespace eTrack\Models\Entities;

/**
 * Student
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|Course[] $courses
 */
class Student extends User
{

    public function courses()
    {
        return $this->belongsToMany('Course', null, 'student_user_id');
    }

}