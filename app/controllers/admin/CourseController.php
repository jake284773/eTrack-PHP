<?php namespace eTrack\Controllers\Admin;

use eTrack\Controllers\RestController;
use eTrack\Courses\CourseRepository;
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