<?php namespace eTrack\Courses\BTEC\National;

use eTrack\GradeCalculators\CourseGradeL3;

class National90CreditDiploma extends National {

    const MAX_UNITS = 9;

    public function __construct()
    {
        parent::__construct();

        array_push($this->possibleGrades, new CourseGradeL3("D*D*", 210, 790));
        array_push($this->possibleGrades, new CourseGradeL3("D*D", 200, 770, 789));
        array_push($this->possibleGrades, new CourseGradeL3("DD", 180, 750, 769));
        array_push($this->possibleGrades, new CourseGradeL3("DM", 160, 720, 749));
        array_push($this->possibleGrades, new CourseGradeL3("MM", 120, 690, 719));
        array_push($this->possibleGrades, new CourseGradeL3("MP", 100, 660, 689));
        array_push($this->possibleGrades, new CourseGradeL3("PP", 60, 630, 659));
        array_push($this->possibleGrades, new CourseGradeL3("NYA", 0, 0, 629));
    }
}