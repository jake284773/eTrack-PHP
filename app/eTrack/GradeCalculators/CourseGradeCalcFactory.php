<?php namespace eTrack\GradeCalculators;

use eTrack\Courses\Course;

class CourseGradeCalcFactory {

    /**
     * Instantiate a course grade calculator object based on the course type
     * from the specified course object.
     *
     * @param Course $course
     * @throws \InvalidArgumentException
     * @return CourseGradeCalc
     */
    public static function create(Course $course)
    {
        if (is_a($course, 'eTrack\Courses\CourseL3')) {
            return new CourseGradeCalcL3();
        } else {
            throw new \InvalidArgumentException("Specified course is unsupported");
        }
    }

} 