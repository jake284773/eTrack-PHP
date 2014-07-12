<?php namespace eTrack\GradeCalculators;

class CourseGradeL3 extends CourseGrade {

    /**
     * The number of UCAS Tariff Points this grade provides.
     * @var integer
     */
    protected $ucasTariffPoints;

    /**
     * Create a new grade object by specifying all the attributes.
     *
     * @param string $grade The name of the grade for the new Grade object.
     * @param integer $ucasTariffPoints The number of UCAS tariff points this
     * grade provides.
     * @param integer $pointsStart The minimum number of points that are applicable
     * for the new Grade object.
     * @param integer $pointsEnd The maximum number of points the student can achieve to be awarded this
     * grade
     */
    public function __construct($grade, $ucasTariffPoints, $pointsStart,
                                $pointsEnd = null)
    {
        parent::__construct($grade, $pointsStart, $pointsEnd);
        $this->ucasTariffPoints = $ucasTariffPoints;
    }

    /**
     * Gets the value of the UCAS tariff points attribute.
     *
     * @return integer
     */
    public function getUcasTariffPoints()
    {
        return $this->ucasTariffPoints;
    }

} 