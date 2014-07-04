<?php namespace eTrack\Courses\BTEC\National;

use eTrack\Assignments\BTEC;
use eTrack\GradeCalculators\BTEC\NationalGrade;

class NationalDiploma extends National {
    public function __construct()
    {
        parent::__construct();

        array_push($this->possibleGrades, new NationalGrade("D*D*", 280, 1060));
        array_push($this->possibleGrades, new NationalGrade("D*D", 260, 1030, 1059));
        array_push($this->possibleGrades, new NationalGrade("DD", 240, 1000, 1029));
        array_push($this->possibleGrades, new NationalGrade("DM", 200, 960, 999));
        array_push($this->possibleGrades, new NationalGrade("MM", 160, 920, 959));
        array_push($this->possibleGrades, new NationalGrade("MP", 120, 880, 919));
        array_push($this->possibleGrades, new NationalGrade("PP", 80, 840, 879));
        array_push($this->possibleGrades, new NationalGrade("NYA", 0, 0, 878));
    }
}