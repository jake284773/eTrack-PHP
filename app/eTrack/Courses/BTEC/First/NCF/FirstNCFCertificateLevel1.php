<?php namespace eTrack\Courses\BTEC\First\NCF;

use eTrack\GradeCalculators\CourseGrade;

class FirstNCFCertificateLevel1 extends FirstNCF {
    protected $level = 1;

    public function __construct()
    {
        array_push($this->possibleGrades, new CourseGrade("P", 24));
    }
} 