<?php namespace eTrack\GradeCalculators;

use eTrack\Courses\Course;
use eTrack\Courses\Unit;

class CoursePointsCalc
{
    private $l2PointMultiplierMap = [
        'Pass' => 5,
        'Merit' => 6,
        'Distinction' => 7
    ];

    private $l3PointMultiplierMap = [
        'Pass' => 7,
        'Merit' => 8,
        'Distinction' => 9
    ];

    public function calculateTotalPoints(Course $course, $studentId)
    {
        switch ($course->level)
        {
            case '2':
                return $this->calculateL2TotalPoints($course, $studentId);
            case '3':
                return $this->calculateL3TotalPoints($course, $studentId);
            default:
                throw new \InvalidArgumentException("Only level 2 and 3 courses ".
                    "are supported.");
        }
    }

    private function calculateL2TotalPoints(Course $course, $studentId)
    {
        $units = $course->units;
        $totalPoints = 0;

        foreach ($units as $unit)
        {
            $totalPoints += $this->calculateL2UnitPoints($unit, $studentId);
        }

        return $totalPoints;
    }

    private function calculateL3TotalPoints(Course $course, $studentId)
    {
        $units = $course->units;
        $totalPoints = 0;

        foreach ($units as $unit)
        {
            $totalPoints += $this->calculateL3UnitPoints($unit, $studentId);
        }

        return $totalPoints;
    }

    private function calculateL3UnitPoints(Unit $unit, $studentId)
    {
        $unitGrade = $this->getUnitGradeForStudent($unit, $studentId);

        if (empty($unitGrade)) {
            return 0;
        }

        return $unit->credit_value * $this->l3PointMultiplierMap[$unitGrade->grade];
    }

    private function calculateL2UnitPoints(Unit $unit, $studentId)
    {
        $unitGrade = $this->getUnitGradeForStudent($unit, $studentId);

        if (empty($unitGrade)) {
            return 0;
        }

        return $unit->credit_value * $this->l2PointMultiplierMap[$unitGrade->grade];
    }

    private function getUnitGradeForStudent(Unit $unit, $studentId)
    {
        return $unit->studentGrades->filter(function($unitGrade) use($studentId) {
            if ($unitGrade->student_user_id == $studentId) {
                return true;
            }

            return false;
        })->first();
    }
} 