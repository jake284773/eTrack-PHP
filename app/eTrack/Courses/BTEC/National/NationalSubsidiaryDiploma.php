<?php namespace eTrack\Courses\BTEC\National;

use eTrack\GradeCalculators\CourseGradeL3;

class NationalSubsidiaryDiploma extends National {

    const MAX_UNITS = 6;

    public function __construct()
    {
        parent::__construct();

        array_push($this->possibleGrades, new CourseGradeL3("D*", 140, 520));
        array_push($this->possibleGrades, new CourseGradeL3("D", 120, 500, 519));
        array_push($this->possibleGrades, new CourseGradeL3("M", 80, 460, 499));
        array_push($this->possibleGrades, new CourseGradeL3("P", 40, 420, 459));
        array_push($this->possibleGrades, new CourseGradeL3("NYA", 0, 0, 419));
    }
}