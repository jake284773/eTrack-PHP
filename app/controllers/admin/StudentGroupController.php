<?php namespace eTrack\Controllers\Admin;

use App;
use eTrack\Accounts\UserRepository;
use eTrack\Controllers\BaseController;
use eTrack\Courses\CourseRepository;
use eTrack\Courses\StudentGroupRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use View;

class StudentGroupController extends BaseController {

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

        foreach ($tutors as $tutor)
        {
            $tutorsSelect[$tutor->id] = $tutor->full_name;
        }

        $students = $course->studentsNotInGroup;

        $studentsSelect = [];

        foreach ($students as $student)
        {
            $studentsSelect[$student->id] = $student->full_name . ' (' . $student->id . ')';
        }

        return View::make('admin.courses.student_groups.create', ['course' => $course,
                                                                  'tutors' => $tutorsSelect,
                                                                  'students' => $studentsSelect]);
    }

    public function show($courseId, $groupId)
    {
        try {
            $this->courseRepository->requireById($courseId);
            $student_group = $this->studentGroupRepository->getWithRelated($groupId);
            return View::make('admin.courses.student_groups.show',
                ['student_group' => $student_group]);
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }
    }

} 