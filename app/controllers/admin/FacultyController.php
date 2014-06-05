<?php namespace eTrack\Controllers\Admin;

use Faculty;
use View;
use Request;
use Redirect;

class FacultyController extends \BaseController {

    public function index()
    {
        $faculties = Faculty::all();

        return View::make('admin.faculties.index', array('faculties' => $faculties));
    }

    public function create()
    {
        return View::make('admin.faculties.create');
    }

    public function show($id)
    {
      $faculty = Faculty::with('courses', 'courses.course_organiser')->where('id', $id)->firstOrFail();

      return View::make('admin.faculties.show', array('faculty' => $faculty));
    }

    public function edit($id)
    {
        $faculty = Faculty::find($id);

        return View::make('admin.faculties.edit', array('faculty' => $faculty));
    }

    public function deleteConfirm($id)
    {
        $faculty = Faculty::find($id);

        if (Request::ajax())
        {
            return View::make('admin.faculties.delete.modal', array('faculty' => $faculty));
        }

        return View::make('admin.faculties.delete.fallback', array('faculty' => $faculty));
    }

    public function destroy($id)
    {
        try {
            $faculty = Faculty::find($id);
            $faculty->delete();
        } catch (QueryException $ex) {
            return Redirect::route('admin.faculties.index')
                ->with('errorMessage', 'Unable to delete faculty');
        }

        return Redirect::route('admin.faculties.index')
            ->with('successMessage', 'Deleted faculty');

    }

}