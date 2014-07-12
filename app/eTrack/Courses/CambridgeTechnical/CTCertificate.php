<?php namespace eTrack\Courses\CambridgeTechnical;

use eTrack\GradeCalculators\CourseGradeL3;

class CTCertificate extends CambridgeTechnical {
    public function __construct()
    {
        array_push($this->possibleGrades, new CourseGradeL3("D*", 70, 260));
        array_push($this->possibleGrades, new CourseGradeL3("D", 60, 250, 259));
        array_push($this->possibleGrades, new CourseGradeL3("M", 40, 230, 249));
        array_push($this->possibleGrades, new CourseGradeL3("P", 20, 210, 229));
    }
} 