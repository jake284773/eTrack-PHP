<?php namespace eTrack\Controllers\Admin;

use App;
use Cache;
use eTrack\Controllers\BaseController;
use eTrack\Courses\CourseRepository;
use eTrack\GradeCalculators\UnitGradeCalc;
use eTrack\Assessment\StudentAssessmentRepository;
use eTrack\Courses\StudentUnit;
use eTrack\Courses\UnitRepository;
use Event;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Input;
use Redirect;
use Validator;
use View;
use LRedis;

class CourseTrackerController extends BaseController
{

    protected $courseRepository;
    protected $unitRepository;
    protected $studentAssessmentRepository;

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
        if (Input::has('group'))
        {
            $filterValidator = Validator::make(
                ['group' => Input::get('group')],
                ['group' => 'exists:student_group,id']
            );

            if ($filterValidator->fails())
            {
                return Redirect::route('admin.courses.tracker.index', $courseId);
            }
        }

        $course = $this->courseRepository->getTrackerRelated($courseId);
        $results = $this->courseRepository
            ->renderCourseUnitGradesForTracker($course, Input::get('group'));

        return View::make('admin.courses.tracker.index', [
            'course' => $course, 'results' => $results
        ]);
    }

    public function unit($courseId, $unitId)
    {
        // Display a 404 page if the requested unit isn't part of the requested
        // course.
        try {
            $this->unitRepository->checkUnitBelongsToCourse($courseId, $unitId);
        } catch (ModelNotFoundException $e) {
            App::abort(404);
        }

        // Validate student group parameter to make sure the group actually
        // exists.
        $filterValidator = Validator::make(
            ['group' => Input::get('group')],
            ['group' => 'exists:student_group,id']
        );

        if ($filterValidator->fails())
        {
            return Redirect::route('admin.courses.tracker.unit',
                [$courseId, $unitId]);
        }

        $course = $this->courseRepository->find($courseId);
        $unit = $this->unitRepository->getWithCriteriaAndAssessments($unitId);

        $results = $this->unitRepository
            ->renderUnitCriteriaAssessmentForTracker($course, $unit,
                Input::get('group'));

        $totalPassCriteria = $this->unitRepository->getTotalCriteria($unit, 'P');
        $totalMeritCriteria = $this->unitRepository->getTotalCriteria($unit, 'M');;
        $totalDistinctionCriteria = $this->unitRepository->getTotalCriteria($unit, 'D');

        return View::make('admin.courses.tracker.unit', [
            'course'           => $course,
            'unit'             => $unit,
            'results'          => $results,
            'totalPass'        => $totalPassCriteria,
            'totalMerit'       => $totalMeritCriteria,
            'totalDistinction' => $totalDistinctionCriteria,
        ]);
    }

    public function calculateFinal($courseId)
    {
        Event::fire('tracker.calcAllFinalGrades', [$courseId]);
        Event::fire('tracker.calcAllPredictedGrades', [$courseId]);

        return Redirect::route('admin.courses.tracker.index', $courseId);
    }

    private function calculateAllUnitGradesForCourse($course)
    {
        $units = $course->units;
        $students = $course->students;

        $unitGradeCalculator = new UnitGradeCalc($this->unitRepository,
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

                if ($studentUnitGrade->grade != 'NYA') {
                    $studentUnitGrade->save();
                }
            }
        }
    }
}