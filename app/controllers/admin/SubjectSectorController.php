<?php namespace eTrack\Controllers\Admin;

use eTrack\Validation\Forms\Admin\SubjectSectors\CreateValidator;
use eTrack\Validation\Forms\Admin\SubjectSectors\EditValidator;
use eTrack\Validation\FormValidationException;
use SubjectSector;
use View;
use Request;
use Redirect;
use Input;
use Illuminate\Database\QueryException;

class SubjectSectorController extends \BaseController {

    /**
     * @var \eTrack\Validation\Forms\Admin\SubjectSectors\CreateValidator
     */
    private $createFormValidator;

    /**
     * @var \eTrack\Validation\Forms\Admin\SubjectSectors\EditValidator
     */
    private $editFormValidator;

    public function __construct(CreateValidator $createValidator, EditValidator $editValidator)
    {
        $this->createFormValidator = $createValidator;
        $this->editFormValidator = $editValidator;
    }

    public function index()
    {
        $subjectSectors = SubjectSector::all();

        return View::make('admin.subjectsectors.index', array('subjectSectors' => $subjectSectors));
    }

    public function create()
    {
        return View::make('admin.subjectsectors.create');
    }

    public function store()
    {
        $formAttributes = array(
            'subject_sector_id' => Input::get('id'),
            'subject_sector_name' => Input::get('name'),
        );

        try {
            $this->createFormValidator->validate($formAttributes);
        } catch (FormValidationException $ex) {
            return Redirect::back()
                ->withInput()
                ->withErrors($ex->getErrors());
        }

        $subjectSector = new SubjectSector();

        $subjectSector->id = $formAttributes['subject_sector_id'];
        $subjectSector->name = $formAttributes['subject_sector_name'];

        $subjectSector->save();

        return Redirect::route('admin.subjectsectors.index')
            ->with('successMessage', 'Created new subject sector');
    }

    public function show($id)
    {
        $subjectSector = SubjectSector::with('courses', 'courses.course_organiser', 'units')->where('id', $id)->firstOrFail();

        return View::make('admin.subjectsectors.show', array('subjectSector' => $subjectSector));
    }

    public function edit($id)
    {
        $subjectSector = SubjectSector::find($id);

        return View::make('admin.subjectsectors.edit', array('subjectSector' => $subjectSector));
    }

    public function update($id)
    {
        $formAttributes = array(
            'subject_sector_name' => Input::get('name'),
        );

        try {
            $this->editFormValidator->validate($formAttributes);
        } catch (FormValidationException $ex) {
            return Redirect::back()
                ->withInput()
                ->withErrors($ex->getErrors());
        }

        $subjectSector = SubjectSector::find($id);

        $subjectSector->name = $formAttributes['subject_sector_name'];

        $subjectSector->save();

        return Redirect::route('admin.subjectsectors.index')
            ->with('successMessage', 'Updated subject sector');
    }

    public function deleteConfirm($id)
    {
        $subjectSector = SubjectSector::find($id);

        if (Request::ajax())
        {
            return View::make('admin.subjectsectors.delete.modal', array('subjectSector' => $subjectSector));
        }

        return View::make('admin.subjectsectors.delete.fallback', array('subjectSector' => $subjectSector));
    }

    public function destroy($id)
    {
        try {
            $subjectSector = SubjectSector::find($id);
            $subjectSector->delete();
        } catch (QueryException $ex) {
            return Redirect::route('admin.subjectsectors.index')
                ->with('errorMessage', 'Unable to delete subject sector');
        }

        return Redirect::route('admin.subjectsectors.index')
            ->with('successMessage', 'Deleted subject sector');
    }

}