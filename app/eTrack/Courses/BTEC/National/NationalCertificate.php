<?php namespace eTrack\Courses\BTEC\National;

use eTrack\GradeCalculators\BTEC\NationalGrade;

class NationalCertificate extends National {
    public function __construct()
    {
        parent::__construct();

        array_push($this->possibleGrades, new NationalGrade("D*", 70, 260));
        array_push($this->possibleGrades, new NationalGrade("D", 60, 250, 259));
        array_push($this->possibleGrades, new NationalGrade("M", 40, 230, 249));
        array_push($this->possibleGrades, new NationalGrade("P", 20, 210, 229));
        array_push($this->possibleGrades, new NationalGrade("NYA", 0, 0, 209));
    }
}