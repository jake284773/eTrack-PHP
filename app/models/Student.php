<?php

/**
 * Student
 *
 * @property string $id
 * @property string $email
 * @property string $password
 * @property string $full_name
 * @property string $role
 * @property string $remember_token
 * @property-read \Illuminate\Database\Eloquent\Collection|\Course[] $courses
 */
class Student extends User {

    public function courses()
    {
        return $this->belongsToMany('Course', null, 'student_user_id');
    }

}