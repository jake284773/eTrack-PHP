<?php namespace eTrack\Controllers\Admin;

use Faculty;
use View;

class FacultyController extends \BaseController {

    public function index()
    {
        $faculties = Faculty::all();

        return View::make('admin.faculties.index', array('faculties' => $faculties));
    }

    public function show($id)
    {
      $faculty = Faculty::with('courses', 'courses.course_organiser')->where('id', $id)->firstOrFail();
//       dd($faculty);

      return View::make('admin.faculties.show', array('faculty' => $faculty));
    }

}