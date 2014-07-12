<?php namespace eTrack\Courses\BTEC\First\QCF;

use eTrack\GradeCalculators\CourseGrade;

class FirstQCFCertificateLevel2 extends FirstQCF {
    public function __construct()
    {
        array_push($this->possibleGrades, new CourseGrade("P", 75, 84));
        array_push($this->possibleGrades, new CourseGrade("M", 85, 94));
        array_push($this->possibleGrades, new CourseGrade("D", 95, 99));
        array_push($this->possibleGrades, new CourseGrade("D*", 100));
    }
} 