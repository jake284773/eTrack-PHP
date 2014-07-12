<?php namespace eTrack\Courses\BTEC\First\NCF;

use eTrack\GradeCalculators\CourseGrade;

class FirstNCFCertificateLevel2 extends FirstNCF {
    public function __construct()
    {
        array_push($this->possibleGrades, new CourseGrade("P", 48, 65));
        array_push($this->possibleGrades, new CourseGrade("M", 66, 83));
        array_push($this->possibleGrades, new CourseGrade("D", 84, 89));
        array_push($this->possibleGrades, new CourseGrade("D*", 90));
    }
} 