<?php namespace eTrack\Models;

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
        return $this->belongsToMany('eTrack\Models\Entities\Course', null, 'student_user_id');
    }

}