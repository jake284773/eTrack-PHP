<?php namespace eTrack\Controllers\Admin;

use App;
use DB;
use eTrack\Controllers\BaseController;
use eTrack\Courses\CourseRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use View;

class StudentAssessmentController extends BaseController {

    protected $courseRepository;

    public function __construct(CourseRepository $courseRepository)
    {
        $this->courseRepository = $courseRepository;
    }

    public function index($courseId, $unitId, $assignmentId, $studentId)
    {
        try {
            $course = $this->courseRepository->getById($courseId);
            $unit = $course->units()->where('id', $unitId)->firstOrFail();
            $assignment = $unit->assignments()
                ->with(['criteria' => function ($query) use ($assignmentId) {
                        $query->join(DB::raw('assignment_criteria ac2'), function ($join) {
                            $join->on('criteria.id', '=', DB::raw('ac2.criteria_id'))
                                ->on('criteria.unit_id', '=', DB::raw('ac2.criteria_unit_id'));
                        });
                        $query->where(DB::raw('ac2.assignment_id'), $assignmentId);
                    }])
                ->where('id', $assignmentId)->firstOrFail();
            $student = $assignment->submissions()->where('student_user_id', '=', $studentId)->firstOrFail();
            $criteriaAssessments = $assignment->assessments()
                ->where('student_assignment_student_user_id', '=', $studentId);
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }

        return View::make('admin.courses.units.assignments.submissions.assess', [
            'course' => $course,
            'unit' => $unit,
            'assignment' => $assignment,
            'student' => $student,
        ]);
    }

} 