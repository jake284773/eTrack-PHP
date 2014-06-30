<?php namespace eTrack\Controllers\Admin;

use eTrack\Controllers\BaseController;
use eTrack\Courses\CourseRepository;
use View;

class CourseController extends BaseController {

    private $courseRepository;

    public function __construct(CourseRepository $courseRepository)
    {
        $this->courseRepository = $courseRepository;
    }

    public function index()
    {
        $courses = $this->courseRepository->getAllPaginated();

        return View::make('admin.courses.index', ['courses' => $courses]);
    }

} 