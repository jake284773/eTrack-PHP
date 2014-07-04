<?php namespace eTrack\Courses\BTEC\National;

use eTrack\GradeCalculators\BTEC\NationalGrade;

class NationalSubsidiaryDiploma extends National {
    public function __construct()
    {
        parent::__construct();

        array_push($this->possibleGrades, new NationalGrade("D*", 140, 520));
        array_push($this->possibleGrades, new NationalGrade("D", 120, 500, 519));
        array_push($this->possibleGrades, new NationalGrade("M", 80, 460, 499));
        array_push($this->possibleGrades, new NationalGrade("P", 40, 420, 459));
        array_push($this->possibleGrades, new NationalGrade("NYA", 0, 0, 419));
    }
}