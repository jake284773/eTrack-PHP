<?php namespace eTrack\Courses\BTEC\National;

use eTrack\GradeCalculators\CourseGradeL3;

class NationalDiploma extends National {

    const MAX_UNITS = 12;

    public function __construct()
    {
        parent::__construct();

        array_push($this->possibleGrades, new CourseGradeL3("D*D*", 280, 1060));
        array_push($this->possibleGrades, new CourseGradeL3("D*D", 260, 1030, 1059));
        array_push($this->possibleGrades, new CourseGradeL3("DD", 240, 1000, 1029));
        array_push($this->possibleGrades, new CourseGradeL3("DM", 200, 960, 999));
        array_push($this->possibleGrades, new CourseGradeL3("MM", 160, 920, 959));
        array_push($this->possibleGrades, new CourseGradeL3("MP", 120, 880, 919));
        array_push($this->possibleGrades, new CourseGradeL3("PP", 80, 840, 879));
        array_push($this->possibleGrades, new CourseGradeL3("NYA", 0, 0, 878));
    }
}