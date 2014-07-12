<?php namespace eTrack\Courses\BTEC\National;

use eTrack\GradeCalculators\CourseGradeL3;

class NationalCertificate extends National {

    const MAX_UNITS = 3;

    public function __construct()
    {
        parent::__construct();

        array_push($this->possibleGrades, new CourseGradeL3("D*", 70, 260));
        array_push($this->possibleGrades, new CourseGradeL3("D", 60, 250, 259));
        array_push($this->possibleGrades, new CourseGradeL3("M", 40, 230, 249));
        array_push($this->possibleGrades, new CourseGradeL3("P", 20, 210, 229));
        array_push($this->possibleGrades, new CourseGradeL3("NYA", 0, 0, 209));
    }
}