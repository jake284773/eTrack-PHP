<?php namespace eTrack\Courses;

use eTrack\GradeCalculators\CourseGradeL3;

class CourseL3 extends Course {

    /**
     * Gets the possible grades array for the course.
     * @return CourseGradeL3[]
     */
    public function getPossibleGrades()
    {
        return $this->possibleGrades;
    }

} 