<?php namespace eTrack\Courses\CambridgeTechnical;

use eTrack\GradeCalculators\CourseGradeL3;

class CTExtendedDiploma extends CambridgeTechnical {
    public function __construct()
    {
        array_push($this->possibleGrades, new CourseGradeL3("D*D*D*", 420, 1590));
        array_push($this->possibleGrades, new CourseGradeL3("D*D*D", 400, 1560, 1589));
        array_push($this->possibleGrades, new CourseGradeL3("D*DD", 380, 1530, 1559));
        array_push($this->possibleGrades, new CourseGradeL3("DDD", 360, 1500, 1529));
        array_push($this->possibleGrades, new CourseGradeL3("DDM", 320, 1460, 1499));
        array_push($this->possibleGrades, new CourseGradeL3("DMM", 280, 1420, 1459));
        array_push($this->possibleGrades, new CourseGradeL3("MMM", 240, 1380, 1419));
        array_push($this->possibleGrades, new CourseGradeL3("MMP", 200, 1340, 1379));
        array_push($this->possibleGrades, new CourseGradeL3("MPP", 160, 1300, 1339));
        array_push($this->possibleGrades, new CourseGradeL3("PPP", 120, 1260, 1299));
    }
}