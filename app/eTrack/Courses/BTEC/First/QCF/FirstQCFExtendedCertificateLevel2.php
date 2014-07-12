<?php namespace eTrack\Courses\BTEC\First\QCF;

use eTrack\GradeCalculators\CourseGrade;

class FirstQCFExtendedCertificateLevel2 extends FirstQCF {
    protected $level = 2;

    public function __construct()
    {
        array_push($this->possibleGrades, new CourseGrade("P", 150, 169));
        array_push($this->possibleGrades, new CourseGrade("M", 170, 189));
        array_push($this->possibleGrades, new CourseGrade("D", 190, 199));
        array_push($this->possibleGrades, new CourseGrade("D*", 200));
    }
} 