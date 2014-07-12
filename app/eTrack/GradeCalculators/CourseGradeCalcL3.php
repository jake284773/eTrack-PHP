<?php namespace eTrack\GradeCalculators;

use eTrack\Courses\CourseL3;

/**
 * Course grade calculator for level 3 courses.
 *
 * This adds support for calculating UCAS tariff points.
 *
 * @package eTrack\GradeCalculators
 */
class CourseGradeCalcL3 extends CourseGradeCalc {

    /**
     * Calculate the number of UCAS tariff points based on the specified number
     * of points and the course object.
     *
     * @param integer $points The total number of points achieved for the
     * qualification.
     * @param CourseL3 $course The level 3 course the calculated grade should be for.
     * This is to determine what the possible grades could be.
     * @throws \InvalidArgumentException When the total number of points doesn't
     * match any of the possible grade boundaries.
     * @return integer The number of UCAS tariff points that the matched grade has.
     */
    public function calcUcasTariffPoints($points, CourseL3 $course)
    {
        // Loop through all the possible grades for the specified BTEC course.
        foreach ($course->getPossibleGrades() as $grading)
        {
            // If the points variable is in the start and end range for the grade,
            // return that grade object.
            if ($points >= $grading->getPointsStart() &&
                $points <= $grading->getPointsEnd())
            {
                return $grading->getUcasTariffPoints();
            }

            // If the points variable exceeds the end range and the start range,
            // and the previous if statement didn't match then return the grade object.
            //
            // This statement should return true when the number of points matches for the
            // highest grade in the qualification.
            if (is_null($grading->getPointsEnd()) && $points >= $grading->getPointsStart())
            {
                return $grading->getUcasTariffPoints();
            }
        }

        // When the number of points cannot be matched to any grade boundary
        // throw an exception.
        throw new \InvalidArgumentException('Invalid number of points provided.');
    }

}