<?php namespace eTrack\GradeCalculators\BTEC;

/**
 * BTEC grade object for storing a grade for a BTEC course.
 *
 * It contains the grade string (i.e. DDM), and the minimum and maximum amount of
 * points the student must have to achieve the grade. These two figures are used
 * when calculating the final grade for a student.
 *
 * When the grade is the highest possible one, the points end attribute is set 
 * to null, which represents that there is no end value for that grade.
 * 
 * @package eTrack\Courses\BTEC
 * @author Jake Moreman <mail@jakemoreman.co.uk>
 * @copyright 2014 City College Plymouth
 */
class BTECGrade {
    
    /**
     * The name of the grade.
     * @var string
     */
    protected $grade;
    
    /**
     * The minimum number of points the student must have achieved to be awarded
     * this grade.
     * @var integer
     */
    protected $pointsStart;
    
    /**
     * The maximum number of points the student can achieve to be awarded this
     * grade.
     *
     * When the student's total number of points exceeds this figure, the program
     * will look at the next grade, and so on...
     *
     * @var integer
     */
    protected $pointsEnd;

    /**
     * Create a new grade object by specifying all the attributes.
     *
     * @param string $grade The name of the grade for the new Grade object.
     * @param integer $pointsStart The minimum number of points that are applicable
     * for the new Grade object.
     * @param integer $pointsEnd The maximum number of points the student can achieve to be awarded this
     * grade.
     */
    public function __construct($grade, $pointsStart, $pointsEnd = null)
    {
        $this->grade = $grade;
        $this->pointsStart = $pointsStart;
        $this->pointsEnd = $pointsEnd;
    }

    /**
     * Gets the value of the grade.
     *
     * @return string
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * Gets the value of pointsStart.
     *
     * @return integer
     */
    public function getPointsStart()
    {
        return $this->pointsStart;
    }

    /**
     * Gets the value of pointsEnd.
     *
     * @return integer
     */
    public function getPointsEnd()
    {
        return $this->pointsEnd;
    }
}