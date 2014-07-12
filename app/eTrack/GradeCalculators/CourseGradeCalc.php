<?php namespace eTrack\GradeCalculators;

use eTrack\Courses\Course;

abstract class CourseGradeCalc
{

    /**
     * Calculate the grade based on the specified number of points and the
     * course object.
     *
     * @param integer $points The total number of points achieved for the
     * qualification.
     * @param Course $course The course the calculated grade should be for.
     * This is to determine what the possible grades could be.
     * @return string
     * @throws \InvalidArgumentException When the total number of points doesn't
     * match any of the possible grade boundaries.
     */
    public function calcGrade($points, Course $course)
    {
        $possibleGrades = $course->getPossibleGrades();

        // Loop through all the possible grades for the specified BTEC course.
        foreach ($possibleGrades as $grading) {
            // If the points variable is in the start and end range for the grade,
            // return that grade object.
            if ($points >= $grading->getPointsStart() &&
                $points <= $grading->getPointsEnd()
            ) {
                return $grading->getGrade();
            }

            // If the points variable exceeds the end range and the start range,
            // and the previous if statement didn't match then return the grade object.
            //
            // This statement should return true when the number of points matches for the
            // highest grade in the qualification.
            if (is_null($grading->getPointsEnd()) && $points >= $grading->getPointsStart()) {
                return $grading->getGrade();
            }
        }

        // When the number of points variable cannot be matched to any grade boundary
        // throw an exception.
        throw new \InvalidArgumentException('Invalid number of points provided');
    }

    public function studentAchievedAllUnits(Course $course, $studentId)
    {
        $numberOfUnitsAchieved = 0;

        $units = $course->units;

        foreach ($units as $unit) {
            $studentGrades = $unit->studentGrades;

            $studentUnitGrade = $studentGrades->filter(function ($studentUnitGrade) use ($studentId) {
                if ($studentUnitGrade->student_user_id == $studentId) {
                    return true;
                }

                return false;
            })->find(0);

            if ($studentUnitGrade->grade != 'NYA') {
                $numberOfUnitsAchieved++;
            }
        }

        if ($numberOfUnitsAchieved == $course::MAX_UNITS) {
            return true;
        }

        return false;
    }
} 