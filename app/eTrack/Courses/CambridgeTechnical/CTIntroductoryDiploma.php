<?php namespace eTrack\Courses\CambridgeTechnical;

use eTrack\GradeCalculators\CourseGradeL3;

class CTIntroductoryDiploma extends CambridgeTechnical {
    public function __construct()
    {
        array_push($this->possibleGrades, new CourseGradeL3("D*", 140, 520));
        array_push($this->possibleGrades, new CourseGradeL3("D", 120, 500, 519));
        array_push($this->possibleGrades, new CourseGradeL3("M", 80, 460, 499));
        array_push($this->possibleGrades, new CourseGradeL3("P", 40, 420, 459));
    }
} 