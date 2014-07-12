<?php namespace eTrack\Courses\BTEC\National;

use eTrack\Courses\CourseL3;

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
abstract class National extends CourseL3 {
    
    /**
     * The qualification framework that the course is for.
     * @var string
     */
    protected $qualificationFramework = 'QCF';

}