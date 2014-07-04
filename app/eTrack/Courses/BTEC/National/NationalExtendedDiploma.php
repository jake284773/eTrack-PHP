<?php namespace eTrack\Courses\BTEC\National;

use eTrack\GradeCalculators\BTEC\NationalGrade;

class NationalExtendedDiploma extends National {
    public function __construct()
    {
        parent::__construct();

        array_push($this->possibleGrades, new NationalGrade("D*D*D*", 420, 1590));
        array_push($this->possibleGrades, new NationalGrade("D*D*D", 400, 1560, 1589));
        array_push($this->possibleGrades, new NationalGrade("D*DD", 380, 1530, 1559));
        array_push($this->possibleGrades, new NationalGrade("DDD", 360, 1500, 1529));
        array_push($this->possibleGrades, new NationalGrade("DDM", 320, 1460, 1499));
        array_push($this->possibleGrades, new NationalGrade("DMM", 280, 1420, 1459));
        array_push($this->possibleGrades, new NationalGrade("MMM", 240, 1380, 1419));
        array_push($this->possibleGrades, new NationalGrade("MMP", 200, 1340, 1379));
        array_push($this->possibleGrades, new NationalGrade("MPP", 160, 1300, 1339));
        array_push($this->possibleGrades, new NationalGrade("PPP", 120, 1260, 1299));
        array_push($this->possibleGrades, new NationalGrade("NYA", 0, 0, 1298));
    }
}