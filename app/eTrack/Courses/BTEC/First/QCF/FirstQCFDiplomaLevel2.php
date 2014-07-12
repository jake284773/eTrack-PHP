<?php namespace eTrack\Courses\BTEC\First\QCF;

use eTrack\GradeCalculators\CourseGrade;

class FirstQCFDiplomaLevel2 extends FirstQCF {
    public function __construct()
    {
        array_push($this->possibleGrades, new CourseGrade("P", 300, 339));
        array_push($this->possibleGrades, new CourseGrade("M", 340, 379));
        array_push($this->possibleGrades, new CourseGrade("D", 380, 399));
        array_push($this->possibleGrades, new CourseGrade("D*", 400));
    }
} 