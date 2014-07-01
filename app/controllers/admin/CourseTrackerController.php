<?php namespace eTrack\Controllers\Admin;

use eTrack\Controllers\BaseController;
use eTrack\Courses\CourseRepository;
use eTrack\Courses\StudentAssessmentRepository;
use eTrack\Courses\StudentUnit;
use eTrack\Courses\UnitRepository;
use eTrack\Courses\StudentUnitGradeCalc;
use View;

class CourseTrackerController extends BaseController
{

    protected $courseRepository;
    protected $unitRepository;
    protected $studentAssessmentRepository;

    protected $assessmentStatusMap = [
        'NYA'  => 'nya',
        'AM'   => 'awaitmark',
        'ALM'  => 'awaitlatemark',
        'A'    => 'achieved',
        'L'    => 'late',
        'LA'   => 'lateachieved',
        'R1'   => 'r1',
        'R1AM' => 'r1awaitmark',
        'R2'   => 'r2',
        'R2AM' => 'r2awaitmark',
        'R3'   => 'r3',
        'R3AM' => 'r3awaitmark',
    ];

    public function __construct(CourseRepository $courseRepository,
                                UnitRepository $unitRepository,
                                StudentAssessmentRepository $studentAssessmentRepository)
    {
        $this->courseRepository = $courseRepository;
        $this->unitRepository = $unitRepository;
        $this->studentAssessmentRepository = $studentAssessmentRepository;
    }

    public function index($courseId)
    {
        $course = $this->courseRepository->getTrackerRelated($courseId);

        $requiredUnitGradeCount = $course->units->count() * $course->students->count();
        $actualUnitGradeCount = StudentUnit::allForCourse($courseId)->count();

        if ($actualUnitGradeCount != $requiredUnitGradeCount) {
            $this->calculateAllUnitGradesForCourse($course);
            $course = $this->courseRepository->getTrackerRelated($courseId);
        }

        return View::make('admin.courses.tracker.index', ['course' => $course]);
    }

    private function calculateAllUnitGradesForCourse($course)
    {
        $units = $course->units;
        $students = $course->students;

        $unitGradeCalculator = new StudentUnitGradeCalc($this->unitRepository,
            $this->studentAssessmentRepository);

        foreach ($students as $student) {
            foreach ($units as $unit) {
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
                $studentUnitGrade->grade = $unitGradeCalculator->calcUnitGrade($student->id, $unit->id);
                $studentUnitGrade->save();
            }
        }
    }
}