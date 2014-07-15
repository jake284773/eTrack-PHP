<?php namespace eTrack\GradeCalculators;

use eTrack\Accounts\Student;
use eTrack\Accounts\UserRepository;
use eTrack\Assessment\StudentAssessmentRepository;
use eTrack\Courses\Course;
use eTrack\Courses\CourseL3;
use eTrack\Courses\CourseRepository;
use eTrack\Courses\StudentUnit;
use eTrack\Courses\Unit;
use eTrack\Courses\UnitRepository;

/**
 * Event handler for all the grade/points calculation events
 * @package eTrack\GradeCalculators
 */
class CalcEventHandler
{

    /**
     * Course repository
     *
     * @var \eTrack\Courses\CourseRepository
     */
    protected $courseRepository;

    /**
     * Unit repository
     *
     * @var \eTrack\Courses\UnitRepository
     */
    protected $unitRepository;

    /**
     * User repository
     *
     * @var \eTrack\Accounts\UserRepository
     */
    protected $userRepository;

    /**
     * Student assessment repository
     *
     * @var \eTrack\Assessment\StudentAssessmentRepository
     */
    protected $studentAssessmentRepository;

    /**
     * Instance of unit grade calculator
     *
     * @var UnitGradeCalc
     */
    protected $unitGradeCalculator;

    /**
     * Inject repository classes and instantiate unit grade calculator.
     *
     * @param CourseRepository $courseRepository
     * @param UnitRepository $unitRepository
     * @param UserRepository $userRepository
     * @param StudentAssessmentRepository $studentAssessmentRepository
     */
    public function __construct(
        CourseRepository $courseRepository,
        UnitRepository $unitRepository,
        UserRepository $userRepository,
        StudentAssessmentRepository $studentAssessmentRepository
    )
    {
        $this->courseRepository = $courseRepository;
        $this->unitRepository = $unitRepository;
        $this->userRepository = $userRepository;
        $this->studentAssessmentRepository = $studentAssessmentRepository;

        $this->unitGradeCalculator = new UnitGradeCalc(
            $this->unitRepository,
            $this->studentAssessmentRepository
        );
    }

    /**
     * Event action for calculating the final grades for all the students
     * in a course.
     *
     * @param $courseId
     */
    public function onAllFinalGrades($courseId)
    {
        $course = $this->courseRepository->getTrackerRelated($courseId);
        $pointsCalc = new CoursePointsCalc();
        $gradeCalc = CourseGradeCalcFactory::create($course);

        foreach ($course->students as $student) {
            if ($course->level == 3) {
                $student = $this->getCalculatedCourseL3Student($student, $course,
                    $pointsCalc, $gradeCalc);
            } else {
                $student = $this->getCalculatedCourseStudent($student, $course,
                    $pointsCalc, $gradeCalc);
            }

            $student->pivot->save();
        }
    }

    /**
     * Event action for calculating the predicted grades for all the students
     * in a course.
     *
     * @param $courseId
     */
    public function onAllPredictedGrades($courseId)
    {
        $course = $this->courseRepository->getTrackerRelated($courseId);
        $pointsCalc = new CoursePredictedPointsCalc();
        $gradeCalc = CourseGradeCalcFactory::create($course);

        foreach ($course->students as $student) {
            if ($course->level == 3) {
                $student = $this->getCalculatedCourseL3Student($student, $course,
                    $pointsCalc, $gradeCalc, 'predicted');
            } else {
                $student = $this->getCalculatedCourseStudent($student, $course,
                    $pointsCalc, $gradeCalc, 'predicted');
            }

            $student->pivot->save();
        }
    }

    /**
     * Event action for calculating the final grade for a specific student and
     * course.
     *
     * @param $courseId
     * @param $studentId
     */
    public function onFinalGradeStudent($courseId, $studentId)
    {
        $course = $this->courseRepository->getTrackerRelated($courseId);

        $pointsCalc = new CoursePointsCalc();
        $gradeCalc = CourseGradeCalcFactory::create($course);

        $student = $course->students()->where('student_user_id', '=', $studentId)->firstOrFail();

        if ($course->level == 3) {
            $student = $this->getCalculatedCourseL3Student($student, $course,
                $pointsCalc, $gradeCalc);
        } else {
            $student = $this->getCalculatedCourseStudent($student, $course,
                $pointsCalc, $gradeCalc);
        }

        $student->pivot->save();
    }

    /**
     * Event action for calculating the predicted grade for a specific student
     * and course.
     *
     * @param $courseId
     * @param $studentId
     */
    public function onPredictedGradeStudent($courseId, $studentId)
    {
        $course = $this->courseRepository->getTrackerRelated($courseId);

        $pointsCalc = new CoursePredictedPointsCalc();
        $gradeCalc = CourseGradeCalcFactory::create($course);

        $student = $course->students()->where('student_user_id', '=', $studentId)->firstOrFail();

        if ($course->level == 3) {
            $student = $this->getCalculatedCourseL3Student($student, $course,
                $pointsCalc, $gradeCalc, 'predicted');
        } else {
            $student = $this->getCalculatedCourseStudent($student, $course,
                $pointsCalc, $gradeCalc, 'predicted');
        }

        $student->pivot->save();
    }

    /**
     * Event action for calculating all unit grades for all students in a
     * specific course.
     *
     * @param $courseId
     */
    public function onUnitGradesCourse($courseId)
    {
        $course = $this->courseRepository->getTrackerRelated($courseId);
        $units = $course->units;
        $students = $course->students;

        foreach ($students as $student) {
            foreach ($units as $unit) {
                $studentUnitGrade = $this->getCalculatedStudentUnit($unit, $student);

                if ($studentUnitGrade->grade != 'NYA') {
                    $studentUnitGrade->save();
                }
            }
        }
    }

    /**
     * Event action for calculating all the unit grades for a specific course
     * and student.
     *
     * @param $courseId
     * @param $studentId
     */
    public function onUnitGradesStudent($courseId, $studentId)
    {
        $course = $this->courseRepository->getTrackerRelated($courseId);
        $units = $course->units;
        $student = $course->students()->where('student_user_id', '=', $studentId)->firstOrFail();

        foreach ($units as $unit) {
            $studentUnitGrade = $this->getCalculatedStudentUnit($unit, $student);

            if ($studentUnitGrade->grade != 'NYA') {
                $studentUnitGrade->save();
            }
        }
    }

    /**
     * Event action for calculating a grade for a specific unit and student.
     *
     * @param $unitId
     * @param $studentId
     */
    public function onUnitGradeStudent($unitId, $studentId)
    {
        $unit = $this->unitRepository->getWithCriteriaAndAssessments($unitId);
        $student = $this->userRepository->find($studentId);

        $studentUnitGrade = $this->getCalculatedStudentUnit($unit, $student);

        if ($studentUnitGrade->grade != 'NYA') {
            $studentUnitGrade->save();
        }
    }

    /**
     * Retrieve and process the student unit record for a specific unit and student.
     *
     * As part of this the unit grade is calculated for the student.
     *
     * @param $unit
     * @param $student
     * @return StudentUnit
     */
    private function getCalculatedStudentUnit(Unit $unit, $student)
    {
        $studentGrades = $unit->studentGrades;

        $studentUnitGrade = $studentGrades->filter(function ($studentUnitGrade) use ($student) {
            if ($studentUnitGrade->student_user_id == $student->id) {
                return true;
            }

            return false;
        })->find(0);

        if (empty($studentUnitGrade)) {
            $studentUnitGrade = new StudentUnit();
        }

        $studentUnitGrade->student_user_id = $student->id;
        $studentUnitGrade->unit_id = $unit->id;
        $studentUnitGrade->grade = $this->unitGradeCalculator->calcUnitGrade($student->id, $unit->id);
        return $studentUnitGrade;
    }

    private function hasStudentAchievedAllUnits(Course $course, $student)
    {
        $numberOfAchievedUnits = 0;

        $units = $course->units;

        foreach ($units as $unit) {
            $studentGrades = $unit->studentGrades;

            $studentUnitGrade = $studentGrades->filter(function ($studentUnitGrade) use ($student) {
                if ($studentUnitGrade->student_user_id == $student->id) {
                    return true;
                }

                return false;
            })->find(0);

            if (isset($studentUnitGrade) && $studentUnitGrade->grade != 'NYA') {
                $numberOfAchievedUnits++;
            }
        }

        if ($numberOfAchievedUnits != $course::MAX_UNITS) {
            return false;
        }

        return true;
    }

    /**
     * Retrieve and process course grade for the specified course and student.
     *
     * @param Student $student
     * @param Course $course
     * @param CoursePointsCalc $pointsCalc
     * @param CourseGradeCalc $gradeCalc
     * @param string $type Final or predicted grades
     * @return Student
     */
    private function getCalculatedCourseStudent(Student $student, Course $course,
                                                  CoursePointsCalc $pointsCalc,
                                                  CourseGradeCalc $gradeCalc,
                                                  $type = 'final')
    {
        $totalPoints = $pointsCalc->calculateTotalPoints($course, $student->id);

        if ($type == 'final' && ! $this->hasStudentAchievedAllUnits($course, $student)) {
            $grade = 'NYA';
        } else {
            $grade = $gradeCalc->calcGrade($totalPoints, $course);
        }

        $student->pivot->{$type . '_grade'} = $grade;

        return $student;
    }

    /**
     * Retrieve and process course grade for the specified level 3 course and
     * student.
     *
     * This method adds calculating UCAS tariff points.
     *
     * @param Student $student
     * @param CourseL3 $course
     * @param CoursePointsCalc $pointsCalc
     * @param CourseGradeCalcL3 $gradeCalc
     * @param string $type Final or predicted grades
     * @return Student
     */
    private function getCalculatedCourseL3Student(Student $student, CourseL3 $course,
                                                  CoursePointsCalc $pointsCalc,
                                                  CourseGradeCalcL3 $gradeCalc,
                                                  $type = 'final')
    {
        $totalPoints = $pointsCalc->calculateTotalPoints($course, $student->id);

        if ($type == 'final' && ! $this->hasStudentAchievedAllUnits($course, $student)) {
            $grade = 'NYA';
            $ucasPoints = 0;
        } else {
            $grade = $gradeCalc->calcGrade($totalPoints, $course);
            $ucasPoints = $gradeCalc->calcUcasTariffPoints($totalPoints, $course);
        }

        $student->pivot->{$type . '_grade'} = $grade;
        $student->pivot->{$type . '_ucas_tariff_score'} = $ucasPoints;

        return $student;
    }
} 