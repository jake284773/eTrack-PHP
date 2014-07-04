<?php namespace eTrack\GradeCalculators\BTEC;

use eTrack\Courses\BTEC\BTEC;

/**
 * BTEC grade calculator object used for calculating a grade for a student based
 * on the total number of points they have achieved from the units.
 *
 * @package eTrack\Courses\BTEC
 * @author Jake Moreman <mail@jakemoreman.co.uk>
 * @copyright 2014 City College Plymouth
 */
class BTECGradeCalc {

    /**
     * Calculate the grade based on the specified number of points and the BTEC
     * course object.
     *
     * @param integer $points The total number of points achieved for the
     * qualification.
     * @param BTEC $btec The BTEC course the calculated grade should be for.
     * This is to determine what the possible grades could be.
     * @return string
     * @throws \InvalidArgumentException When the total number of points doesn't
     * match any of the possible grade boundaries.
     */
    public function calcGrade($points, BTEC $btec)
    {
        $possibleGrades = $btec->getPossibleGrades();

        // Loop through all the possible grades for the specified BTEC course.
        foreach ($possibleGrades as $grading) {
            // If the points variable is in the start and end range for the grade,
            // return that grade object.
            if ($points >= $grading->getPointsStart() &&
                $points <= $grading->getPointsEnd())
            {
                return $grading->getGrade();
            }

            // If the points variable exceeds the end range and the start range,
            // and the previous if statement didn't match then return the grade object.
            //
            // This statement should return true when the number of points matches for the
            // highest grade in the qualification.
            if (is_null($grading->getPointsEnd()) && $points >= $grading->getPointsStart())
            {
                return $grading->getGrade();
            }
        }

        // When the number of points variable cannot be matched to any grade boundary
        // throw an exception.
        throw new \InvalidArgumentException('Invalid number of points provided');
    }

//    public function calcPredictedGrade()

}