<?php namespace eTrack\Courses\BTEC\National;

use eTrack\Courses\BTEC\BTEC;
use eTrack\GradeCalculators\BTEC\NationalGrade;

/**
 * Abstract BTEC National course entity.
 *
 * All BTEC National courses inherit from this class. It defines the common
 * attributes and methods which are used with all BTEC National courses.
 *
 * @abstract
 * @package eTrack\Courses\BTEC\National
 * @author Jake Moreman <mail@jakemoreman.co.uk>
 * @copyright 2014 City College Plymouth
 */
abstract class National extends BTEC {
    
    /**
     * The qualification level for this course.
     *
     * This is pre-defined as all BTEC Nationals are at level 3.
     *
     * @var integer
     */
    protected $level = 3;
    
    /**
     * The qualification framework that the course is for.
     * @var string
     */
    protected $qualificationFramework = 'QCF';
    
    /**
     * An array of BTEC National Grade objects
     * @var NationalGrade[]
     */
    protected $possibleGrades = array();

    public function getGrade($grade)
    {
        foreach ($this->possibleGrades as $grading)
        {
            if ($grading->getGrade() === $grade) return $grading;
        }

        throw new \InvalidArgumentException('Specified grade could not be found');
    }

    /**
     * Gets all the possible grades for the course/
     *
     * @return NationalGrade[]
     */
    public function getPossibleGrades()
    {
        return $this->possibleGrades;
    }

}