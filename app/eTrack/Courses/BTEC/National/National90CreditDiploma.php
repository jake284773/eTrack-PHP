<?php namespace eTrack\Courses\BTEC\National;

use eTrack\GradeCalculators\BTEC\NationalGrade;

class National90CreditDiploma extends National {
    public function __construct()
    {
        parent::__construct();

        array_push($this->possibleGrades, new NationalGrade("D*D*", 210, 790));
        array_push($this->possibleGrades, new NationalGrade("D*D", 200, 770, 789));
        array_push($this->possibleGrades, new NationalGrade("DD", 180, 750, 769));
        array_push($this->possibleGrades, new NationalGrade("DM", 160, 720, 749));
        array_push($this->possibleGrades, new NationalGrade("MM", 120, 690, 719));
        array_push($this->possibleGrades, new NationalGrade("MP", 100, 660, 689));
        array_push($this->possibleGrades, new NationalGrade("PP", 60, 630, 659));
        array_push($this->possibleGrades, new NationalGrade("NYA", 0, 0, 629));
    }
}