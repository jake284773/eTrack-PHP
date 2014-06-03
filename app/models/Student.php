<?php

class Student extends User {

    public function courses()
    {
        return $this->belongsToMany('Course', null, 'student_user_id');
    }

}