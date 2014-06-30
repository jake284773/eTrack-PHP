<?php namespace eTrack\Controllers\Admin;

use App;
use eTrack\Controllers\BaseController;
use eTrack\Courses\StudentGroupRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use View;

class StudentGroupController extends BaseController {

    protected $studentGroupRepository;

    public function __construct(StudentGroupRepository $studentGroupRepository)
    {
        $this->studentGroupRepository = $studentGroupRepository;
    }

    public function show($courseId, $groupId)
    {
        try {
            $student_group = $this->studentGroupRepository->getWithRelated($groupId);
            return View::make('admin.courses.student_groups.show',
                ['student_group' => $student_group]);
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }
    }

} 