<?php namespace eTrack\Controllers\Admin;

use App;
use DB;
use eTrack\Accounts\Student;
use eTrack\Assignments\AssignmentSubmissionRepository;
use eTrack\Controllers\BaseController;
use eTrack\Courses\CourseRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Input;
use Redirect;
use Request;
use View;

class StudentSubmissionController extends BaseController
{

    private $courseRepository;
    private $submissionRepository;

    public function __construct(CourseRepository $courseRepository,
                                AssignmentSubmissionRepository $submissionRepository)
    {
        $this->courseRepository = $courseRepository;
        $this->submissionRepository = $submissionRepository;
    }

    public function add($courseId, $unitId, $assignmentId)
    {
        try {
            $course = $this->courseRepository->getById($courseId);
            $unit = $course->units()->where('id', $unitId)->firstOrFail();
            $assignment = $unit->assignments()->where('id', $assignmentId)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }

        $studentsAlreadySubmitted = DB::table('student_assignment')
            ->select('student_assignment.student_user_id')
            ->join('course_student', 'student_assignment.student_user_id', '=', 'course_student.student_user_id')
            ->where('course_student.course_id', '=', $courseId)
            ->where('student_assignment.assignment_id', '=', $assignmentId)
            ->lists('student_user_id');

        if ($studentsAlreadySubmitted) {
            $students = $course->students()
                ->whereNotIn('id', $studentsAlreadySubmitted)
                ->get();
        } else {
            $students = $course->students;
        }

        $studentsSelect = ['' => ''];

        foreach ($students as $student) {
            $studentsSelect[$student->id] = $student->full_name . ' (' . $student->id . ')';
        }

        return View::make('admin.courses.units.assignments.submissions.add', [
            'course'     => $course,
            'unit'       => $unit,
            'students'   => $studentsSelect,
            'assignment' => $assignment,
        ]);
    }

    public function store($courseId, $unitId, $assignmentId)
    {
        try {
            $course = $this->courseRepository->getById($courseId);
            $unit = $course->units()->where('id', $unitId)->firstOrFail();
            $assignment = $unit->assignments()->where('id', $assignmentId)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }

        $formData = Input::all();

        // Remove any special deadline information if the checkbox wasn't ticked
        if (!isset($formData['special_deadline_required'])) {
            $specialDeadlineFields = [
                'special_deadline_required',
                'special_deadline_date_string',
                'special_deadline_hour',
                'special_deadline_minute'
            ];

            $formData = array_diff($formData, $specialDeadlineFields);
        }

        $submission = $this->submissionRepository->getNew($formData);
        $submission->assignment_id = $assignmentId;

        if (!$submission->isValid()) {
            return Redirect::back()->withInput()->withErrors($submission->getErrors());
        }

        try {
            DB::transaction(function () use ($submission) {
                $submission->save();
            });
        } catch (\Exception $e) {
            return Redirect::back()->withInput()->with('errorMessage',
                'Unable to add student assignment to database.');
        }

        return Redirect::route('admin.courses.units.assignments.show',
            [$course->id, $unit->id, $assignment->id])
            ->with('successMessage', 'Added student submission.');
    }

    public function edit($courseId, $unitId, $assignmentId, $studentId)
    {
        try {
            $course = $this->courseRepository->getById($courseId);
            $unit = $course->units()->where('id', $unitId)->firstOrFail();
            $assignment = $unit->assignments()->where('id', $assignmentId)->firstOrFail();
            $submission = $assignment->submissions()->where('student_user_id', '=', $studentId)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }

        return View::make('admin.courses.units.assignments.submissions.edit', [
            'course'     => $course,
            'unit'       => $unit,
            'assignment' => $assignment,
            'student'    => $submission,
        ]);
    }

    public function show()
    {
    }

    public function deleteConfirm($courseId, $unitId, $assignmentId, $studentId)
    {
        try {
            $course = $this->courseRepository->getById($courseId);
            $unit = $course->units()->where('id', $unitId)->firstOrFail();
            $assignment = $unit->assignments()->where('id', $assignmentId)->firstOrFail();
            $student = $assignment->submissions()->where('student_user_id', '=', $studentId)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }

        if (Request::ajax()) {
            return View::make('admin.courses.units.assignments.submissions.delete.modal', [
                'course'     => $course,
                'unit'       => $unit,
                'assignment' => $assignment,
                'student'    => $student,
            ]);
        }

        return View::make('admin.courses.units.assignments.submissions.delete.fallback', [
            'course'     => $course,
            'unit'       => $unit,
            'assignment' => $assignment,
            'student'    => $student,
        ]);
    }

    public function destroy($courseId, $unitId, $assignmentId, $studentId)
    {
        try {
            $course = $this->courseRepository->getById($courseId);
            $unit = $course->units()->where('id', $unitId)->firstOrFail();
            $assignment = $unit->assignments()->where('id', $assignmentId)->firstOrFail();
            $criteriaAssessments = $assignment->assessments()
                ->where('student_assignment_student_user_id', '=', $studentId);
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }

        try {
            DB::transaction(function () use ($assignment, $criteriaAssessments, $studentId) {
                $criteriaAssessments->delete();
                $assignment->submissions()->detach($studentId);
            });
        } catch (\Exception $e) {
            return Redirect::route('admin.courses.units.assignments.show',
                [$courseId, $unitId, $assignmentId])
                ->with('errorMessage', 'Unable to delete student submission.');
        }

        return Redirect::route('admin.courses.units.assignments.show',
            [$courseId, $unitId, $assignmentId])
            ->with('successMessage', 'Deleted student submission.');
    }

} 