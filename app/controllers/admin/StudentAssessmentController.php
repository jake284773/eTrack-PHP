<?php namespace eTrack\Controllers\Admin;

use App;
use DB;
use eTrack\Controllers\BaseController;
use eTrack\Courses\CourseRepository;
use eTrack\Courses\UnitRepository;
use eTrack\Assignments\AssignmentRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use View;
use Input;

class StudentAssessmentController extends BaseController {

    protected $courseRepository;
    protected $unitRepository;
    protected $assignmentRepository;

    public function __construct(CourseRepository $courseRepository,
                                UnitRepository $unitRepository,
                                AssignmentRepository $assignmentRepository)
    {
        $this->courseRepository = $courseRepository;
        $this->unitRepository = $unitRepository;
        $this->assignmentRepository = $assignmentRepository;
    }

    public function index($courseId, $unitId, $assignmentId, $studentId)
    {
        try {
            $course = $this->courseRepository->getById($courseId);
            $unit = $this->unitRepository->getById($unitId);
            $assignment = $this->assignmentRepository
                ->getWithSubmissionsAndCriteria($assignmentId);
            $student = $assignment->submissions()
                ->where('student_user_id', '=', $studentId)->firstOrFail();
            $criteriaAssessments = $assignment->assessments()
                ->where('student_assignment_student_user_id', '=', $studentId)
                ->get();
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }

        $assessmentRecords = [];

        foreach ($assignment->criteria as $criteria)
        {
            $criteriaAssessment = $criteriaAssessments->filter(function ($assessment) use ($criteria) {
                if ($assessment->criteria_id == $criteria->id) {
                    return true;
                }

                return false;
            })->find(0);

            if ($criteriaAssessment) {
                $assessmentRecords[$criteria->id] = $criteriaAssessment->assessment_status;
            } else {
                $assessmentRecords[$criteria->id] = '';
            }
        }

        return View::make('admin.courses.units.assignments.submissions.assess', [
            'course' => $course,
            'unit' => $unit,
            'assignment' => $assignment,
            'student' => $student,
            'assessmentRecords' => $assessmentRecords,
        ]);
    }

    public function store($courseId, $unitId, $assignmentId, $studentId)
    {
        try {
            $course = $this->courseRepository->getById($courseId);
            $unit = $this->unitRepository->getById($unitId);
            $assignment = $this->assignmentRepository
                ->getWithSubmissionsAndCriteria($assignmentId);
            $student = $assignment->submissions()
                ->where('student_user_id', '=', $studentId)->firstOrFail();
            $criteriaAssessments = $assignment->assessments()
                ->where('student_assignment_student_user_id', '=', $studentId)
                ->get();
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }
        
        return dd($criteriaAssessments->toArray());

        return dd(Input::all());
    }

}