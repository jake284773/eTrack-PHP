<?php namespace eTrack\GradeCalculators;

use eTrack\Courses\Course;
use eTrack\Courses\StudentUnit;
use eTrack\Courses\Unit;

/**
 * Calculation class for calculating the total number of points a student has
 * achieved for a course.
 *
 * @package eTrack\GradeCalculators
 */
class CoursePointsCalc
{
    /**
     * Points per credit for level 2 units based on the unit grade.
     *
     * @var array
     */
    protected $l2PointMultiplierMap = [
        'Pass' => 5,
        'Merit' => 6,
        'Distinction' => 7
    ];

    /**
     * Points per credit for level 3 units based on the unit grade.
     *
     * @var array
     */
    protected $l3PointMultiplierMap = [
        'Pass' => 7,
        'Merit' => 8,
        'Distinction' => 9
    ];

    /**
     * Calculate the total number of points for the specified student and course.
     *
     * Acts like a facade to the actual calculation methods.
     *
     * @param Course $course
     * @param $studentId
     * @return int
     * @throws \InvalidArgumentException
     */
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

    /**
     * Calculate the total number of points a student has achieved for the
     * specified level 2 course.
     *
     * @param Course $course
     * @param $studentId
     * @return int
     */
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

    /**
     * Calculate the total number of points a student has achieved for the
     * specified level 3 course.
     *
     * @param Course $course
     * @param $studentId
     * @return int
     */
    protected function calculateL3TotalPoints(Course $course, $studentId)
    {
        $units = $course->units;
        $totalPoints = 0;

        // If the course doesn't have the correct number of units then no grade
        // can be awarded, so return 0 points.
        if ($units->count() != $course::MAX_UNITS) {
            return 0;
        }

        foreach ($units as $unit)
        {
            $totalPoints += $this->calculateL3UnitPoints($unit, $studentId);
        }

        return $totalPoints;
    }

    /**
     * Retrieve the number of points a student has achieved for the specified
     * level 3 unit.
     *
     * @param Unit $unit
     * @param $studentId
     * @return int
     */
    protected function calculateL3UnitPoints(Unit $unit, $studentId)
    {
        $unitGrade = $this->getUnitGradeForStudent($unit, $studentId);

        if (empty($unitGrade)) {
            return 0;
        }

        return $unit->credit_value * $this->l3PointMultiplierMap[$unitGrade->grade];
    }

    /**
     * Retrieve the number of points a student has achieved for a specified
     * level 2 unit.
     *
     * @param Unit $unit
     * @param $studentId
     * @return int
     */
    protected function calculateL2UnitPoints(Unit $unit, $studentId)
    {
        $unitGrade = $this->getUnitGradeForStudent($unit, $studentId);

        if (empty($unitGrade)) {
            return 0;
        }

        return $unit->credit_value * $this->l2PointMultiplierMap[$unitGrade->grade];
    }

    /**
     * Retrieve
     *
     * @param Unit $unit
     * @param $studentId
     * @return mixed
     */
    protected function getUnitGradeForStudent(Unit $unit, $studentId)
    {
        return $unit->studentGrades->filter(function($unitGrade) use($studentId) {
            if ($unitGrade->student_user_id == $studentId) {
                return true;
            }

            return false;
        })->first();
    }
} 