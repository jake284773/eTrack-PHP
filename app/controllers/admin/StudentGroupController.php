<?php namespace eTrack\Controllers\Admin;

use App;
use DB;
use eTrack\Accounts\UserRepository;
use eTrack\Controllers\BaseController;
use eTrack\Courses\CourseRepository;
use eTrack\Courses\StudentGroupRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Input;
use Redirect;
use Request;
use View;

class StudentGroupController extends BaseController
{

    protected $courseRepository;
    protected $studentGroupRepository;
    protected $userRepository;

    public function __construct(CourseRepository $courseRepository,
                                StudentGroupRepository $studentGroupRepository,
                                UserRepository $userRepository)
    {
        $this->courseRepository = $courseRepository;
        $this->studentGroupRepository = $studentGroupRepository;
        $this->userRepository = $userRepository;
    }

    public function create($courseId)
    {
        try {
            $course = $this->courseRepository->requireById($courseId);
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }

        $tutors = $this->userRepository->getAllTutors();

        $tutorsSelect = ['' => ''];

        foreach ($tutors as $tutor) {
            $tutorsSelect[$tutor->id] = $tutor->full_name;
        }

        $students = $course->studentsNotInGroup;

        $studentsSelect = [];

        foreach ($students as $student) {
            $studentsSelect[$student->id] = $student->full_name . ' (' . $student->id . ')';
        }

        return View::make('admin.courses.student_groups.create', ['course'   => $course,
                                                                  'tutors'   => $tutorsSelect,
                                                                  'students' => $studentsSelect]);
    }

    public function store($courseId)
    {
        try {
            $course = $this->courseRepository->requireById($courseId);
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }

        $studentGroup = $this->studentGroupRepository->getNew(Input::all());
        $studentGroup->course_id = $courseId;

        if (!$studentGroup->isValid()) {
            return Redirect::back()->withInput()->withErrors($studentGroup->getErrors());
        }

        try {
            DB::transaction(function () use ($studentGroup) {
                $studentGroup->save();
                $studentGroup->students()->sync(Input::get('students'));
            });
        } catch (\Exception $e) {
            return Redirect::back()->withInput()->with('errorMessage',
                'Unable to save new student group to database.');
        }

        return Redirect::route('admin.courses.show', [$course->id, '#groups'])
            ->with('successMessage', 'Created new student group');
    }

    public function edit($courseId, $groupId)
    {
        try {
            $course = $this->courseRepository->requireById($courseId);
            $studentGroup = $this->studentGroupRepository->getWithRelated($groupId);
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }

        $tutors = $this->userRepository->getAllTutors();

        $tutorsSelect = ['' => ''];

        foreach ($tutors as $tutor) {
            $tutorsSelect[$tutor->id] = $tutor->full_name;
        }

        $students = $course->studentsNotInGroup;

        $studentsSelect = [];

        foreach ($students as $student) {
            $studentsSelect[$student->id] = $student->full_name . ' (' . $student->id . ')';
        }

        return View::make('admin.courses.student_groups.edit', [
            'course'   => $course,
            'tutors' => $tutorsSelect,
            'students' => $studentsSelect,
            'studentGroup' => $studentGroup,
        ]);
    }

    public function update($courseId, $groupId)
    {

    }

    public function show($courseId, $groupId)
    {
        try {
            $course = $this->courseRepository->requireById($courseId);
            $student_group = $this->studentGroupRepository->getWithRelated($groupId);
            return View::make('admin.courses.student_groups.show',
                ['course' => $course, 'student_group' => $student_group]);
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }
    }

    public function deleteConfirm($courseId, $groupId)
    {
        try {
            $course = $this->courseRepository->requireById($courseId);
            $studentGroup = $this->studentGroupRepository->getWithRelated($groupId);
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }

        if (Request::ajax()) {
            return View::make('admin.courses.student_groups.delete.modal',
                ['course' => $course, 'studentGroup' => $studentGroup]);
        }

        return View::make('admin.courses.student_groups.delete.fallback',
            ['course' => $course, 'studentGroup' => $studentGroup]);
    }

    public function destroy($courseId, $groupId)
    {
        try {
            $this->courseRepository->requireById($courseId);
            $studentGroup = $this->studentGroupRepository->getWithRelated($groupId);
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }

        try {
            DB::transaction(function () use ($studentGroup) {
                $studentGroup->students()->detach();
                $studentGroup->delete();
            });
        } catch (\Exception $e) {
            return Redirect::back()->with('errorMessage', 'Unable to delete student group.');
        }

        return Redirect::route('admin.courses.show', $courseId)->with('successMessage', 'Deleted student group');
    }

} 