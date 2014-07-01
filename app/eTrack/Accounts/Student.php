<?php namespace eTrack\Accounts;

use eTrack\Courses\Course;

/**
 * Student
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|Course[] $courses
 * @property string $id
 * @property string $email
 * @property string $password
 * @property string $full_name
 * @property string $role
 * @property string $remember_token
 */
class Student extends User
{

    public function courses()
    {
        return $this->belongsToMany('eTrack\Courses\Course', null, 'student_user_id');
    }

    public function unitGrades()
    {
        return $this->hasMany('eTrack\Courses\StudentUnit', 'student_user_id')
            ->join('unit', 'student_unit.unit_id', '=', 'unit.id')
            ->orderBy('number');
    }



}