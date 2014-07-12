<?php namespace eTrack\GradeCalculators;

use eTrack\Courses\Course;
use eTrack\Courses\StudentUnit;
use eTrack\Courses\Unit;

class CoursePredictedPointsCalc extends CoursePointsCalc
{
    protected function calculateL2TotalPoints(Course $course, $studentId)
    {
        $units = $course->units;
        $totalPoints = 0;

        foreach ($units as $unit)
        {
            $totalPoints += $this->calculateL2UnitPoints($unit, $studentId);
        }

        return $totalPoints;
    }

    protected function calculateL3TotalPoints(Course $course, $studentId)
    {
        $units = $course->units;
        $totalPoints = 0;

        if ($units->count() < $course::MAX_UNITS) {
            $numberOfUnitsMissing = $course::MAX_UNITS - $units->count();
            $totalPoints = (10 * $this->l3PointMultiplierMap['Pass']) * $numberOfUnitsMissing;
        }

        foreach ($units as $unit)
        {
            $totalPoints += $this->calculateL3UnitPoints($unit, $studentId);
        }

        return $totalPoints;
    }

    protected function calculateL3UnitPoints(Unit $unit, $studentId)
    {
        $unitGrade = $this->getUnitGradeForStudent($unit, $studentId);

        // Predictions assume that the student will pass the course.
        // So all units which still have an NYA will be given a Pass grade
        // for the purpose of calculation final grade predictions.
        if (empty($unitGrade) or $unitGrade->grade == 'NYA') {
            $unitGrade = new StudentUnit();
            $unitGrade->grade = 'Pass';
        }

        return $unit->credit_value * $this->l3PointMultiplierMap[$unitGrade->grade];
    }

    protected function calculateL2UnitPoints(Unit $unit, $studentId)
    {
        $unitGrade = $this->getUnitGradeForStudent($unit, $studentId);

        if (empty($unitGrade)) {
            return 0;
        }

        // Predictions assume that the student will pass the course.
        // So all units which still have an NYA will be given a Pass grade
        // for the purpose of calculation final grade predictions.
        if ($unitGrade->grade == 'NYA') {
            $unitGrade->grade = 'Pass';
        }

        return $unit->credit_value * $this->l2PointMultiplierMap[$unitGrade->grade];
    }
} 