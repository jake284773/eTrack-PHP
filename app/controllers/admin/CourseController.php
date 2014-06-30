<?php namespace eTrack\Controllers\Admin;

use App;
use eTrack\Controllers\RestController;
use eTrack\Courses\CourseRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use View;

class CourseController extends RestController {

    private $courseRepository;

    public function __construct(CourseRepository $courseRepository)
    {
        $this->courseRepository = $courseRepository;
    }

    public function index()
    {
        $courses = $this->courseRepository->paginatedAllRelated();

        return View::make('admin.courses.index', ['courses' => $courses]);
    }

    public function create()
    {

    }

    public function store()
    {

    }

    public function show($id)
    {
        try {
            $course = $this->courseRepository->getWithRelated($id);

            return View::make('admin.courses.show', ['course' => $course]);
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }
    }

    public function edit($id)
    {

    }

    public function update($id)
    {

    }

    public function deleteConfirm($id)
    {

    }

    public function destroy($id)
    {

    }

} 