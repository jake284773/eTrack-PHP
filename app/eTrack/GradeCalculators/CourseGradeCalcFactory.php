<?php namespace eTrack\GradeCalculators;

use eTrack\Courses\BTEC\National\National;
use eTrack\Courses\Course;
use eTrack\GradeCalculators\BTEC\NationalGradeCalc;

class CourseGradeCalcFactory {

    /**
     * Instantiate a course grade calculator object based on the course type
     * from the specified course object.
     *
     * @param Course $course
     * @throws \InvalidArgumentException
     * @return \eTrack\GradeCalculators\BTEC\NationalGradeCalc
     */
    public static function create(Course $course)
    {
        if (is_a($course, 'eTrack\Courses\BTEC\National\National')) {
            return new NationalGradeCalc();
        } else {
            throw new \InvalidArgumentException("Specified course is unsupported");
        }
    }

} 