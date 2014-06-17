<?php namespace eTrack\Controllers\Admin;

use eTrack\Validation\Forms\Admin\Faculties\CreateValidator;
use eTrack\Validation\Forms\Admin\Faculties\EditValidator;
use eTrack\Validation\FormValidationException;
use Faculty;
use View;
use Request;
use Redirect;
use Input;
use Illuminate\Database\QueryException;

class FacultyController extends \BaseController {

    /**
     * @var \eTrack\Validation\Forms\Admin\Faculties\CreateValidator
     */
    protected $createFormValidator;

    /**
     * @var \eTrack\Validation\Forms\Admin\Faculties\EditValidator
     */
    protected $editFormValidator;

    public function __construct(CreateValidator $createValidator, EditValidator $editValidator)
    {
        $this->createFormValidator = $createValidator;
        $this->editFormValidator = $editValidator;
    }

    public function index()
    {
        $faculties = Faculty::all();

        return View::make('admin.faculties.index', array('faculties' => $faculties));
    }

    public function create()
    {
        return View::make('admin.faculties.create');
    }

    public function store()
    {
        $formAttributes = array(
            'faculty_code' => Input::get('id'),
            'faculty_name' => Input::get('name'),
        );

        try {
            $this->createFormValidator->validate($formAttributes);
        } catch (FormValidationException $ex) {
            return Redirect::back()
                ->withInput()
                ->withErrors($ex->getErrors());
        }

        $faculty = new Faculty();

        $faculty->id = $formAttributes['faculty_code'];
        $faculty->name = $formAttributes['faculty_name'];

        $faculty->save();

        return Redirect::route('admin.faculties.index')
            ->with('successMessage', 'Created new faculty');
    }

    public function show($id)
    {
      $faculty = Faculty::with('courses', 'courses.course_organiser')->where('id', $id)->firstOrFail();

      return View::make('admin.faculties.show', array('faculty' => $faculty));
    }

    public function edit($id)
    {
        $faculty = Faculty::find($id);

        if (! $faculty) {
            return App::abort(404);
        }

        return View::make('admin.faculties.edit', array('faculty' => $faculty));
    }

    public function update($id)
    {
        $formAttributes = array(
            'faculty_name' => Input::get('name'),
        );

      try {
            $this->editFormValidator->validate($formAttributes);
        } catch (FormValidationException $ex) {
            return Redirect::back()
                ->withInput()
                ->withErrors($ex->getErrors());
        }

        $faculty = Faculty::find($id);

        $faculty->name = $formAttributes['faculty_name'];

        $faculty->save();

        return Redirect::route('admin.faculties.index')
            ->with('successMessage', 'Updated faculty');
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