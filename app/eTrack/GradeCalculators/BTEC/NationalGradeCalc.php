<?php namespace eTrack\GradeCalculators\BTEC;

use eTrack\Courses\BTEC\BTEC;
use eTrack\Courses\BTEC\National\National;

/**
 * BTEC National grade calculator object used for calculating a grade for a
 * student, based on the total number of points they have achieved from the units.
 *
 * @package eTrack\Courses\BTEC\National
 * @author Jake Moreman <mail@jakemoreman.co.uk>
 * @copyright 2014 City College Plymouth
 */
class NationalGradeCalc extends BTECGradeCalc {
    /**
     * Calculate the number of UCAS tariff points based on the specified number
     * of points and the BTEC course object. 
     *
     * @param integer $points The total number of points achieved for the
     * qualification.
     * @param National $btec The BTEC course the calculated grade should be for.
     * This is to determine what the possible grades could be.
     * @throws \InvalidArgumentException When the total number of points doesn't
     * match any of the possible grade boundaries.
     * @return integer The number of UCAS tariff points that the matched grade has.
     */
    public function calcUcasTariffPoints($points, National $btec)
    {
        // Loop through all the possible grades for the specified BTEC course.
        foreach ($btec->getPossibleGrades() as $grading)
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

            if ($points == 0)
            {
                return $grading->getUcasTariffPoints();
            }
        }

        // When the number of points cannot be matched to any grade boundary
        // throw an exception.
        throw new \InvalidArgumentException('Invalid number of points provided.');
    }
}