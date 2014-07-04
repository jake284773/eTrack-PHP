<?php namespace eTrack\Courses\BTEC;

use eTrack\Courses\Course;
use eTrack\GradeCalculators\BTEC\BTECGrade;

/**
 * Abstract BTEC course entity.
 *
 * All BTEC courses inherit from this class. It defines the common attributes
 * and methods which are used with all BTEC courses.
 *
 * @abstract
 */
abstract class BTEC extends Course {
    
    /**
     * An array of all the possible grades in the form of grade objects.
     * @var BTECGrade[]
     */
    protected $possibleGrades = array();
    
    /**
     * The total number of credit points this course provides.
     * @var integer
     */
    protected $totalCredits;

    /**
     * Retrieves the BTECGrade object based on the grade string parameter.
     *
     * @param string $grade The grade to search for in the shorthand format
     * (i.e. DDM).
     * @throws \InvalidArgumentException
     * @return BTECGrade
     */
    public function getGrade($grade)
    {
        foreach ($this->possibleGrades as $grading)
        {
            if ($grading->getGrade() === $grade)
            {
                return $grading;
            }
        }

        throw new \InvalidArgumentException('Specified grade could not be found.');
    }

    /**
     * Gets the possible grades array for the BTEC course.
     * @return BTECGrade[]
     */
    public function getPossibleGrades()
    {
        return $this->possibleGrades;
    }
}