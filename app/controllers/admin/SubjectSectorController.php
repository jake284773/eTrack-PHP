<?php namespace eTrack\Controllers\Admin;

use App;
use eTrack\Controllers\BaseController;
use eTrack\SubjectSectors\SubjectSectorRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use View;
use Request;
use Redirect;
use Input;
use Illuminate\Database\QueryException;

class SubjectSectorController extends BaseController {

    protected $subjectSectorRepository;

    public function __construct(SubjectSectorRepository $subjectSectorRepository)
    {
        $this->subjectSectorRepository = $subjectSectorRepository;
    }

    public function index()
    {
        $subjectSectors = $this->subjectSectorRepository->getAll();

        return View::make('admin.subjectsectors.index', ['subjectSectors' => $subjectSectors]);
    }

    public function create()
    {
        return View::make('admin.subjectsectors.create');
    }

    public function store()
    {
        $subjectSector = $this->subjectSectorRepository->getNew(Input::all());

        if (! $subjectSector->isValid()) {
            return Redirect::back()->withInput()->withErrors($subjectSector->getErrors());
        }

        $subjectSector->save();

        return Redirect::route('admin.subjectsectors.index')
            ->with('successMessage', 'Created new subject sector');
    }

    public function show($id)
    {
        try {
            $subjectSector = $this->subjectSectorRepository->getWithRelated($id);
            return View::make('admin.subjectsectors.show', ['subjectSector' => $subjectSector]);
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }
    }

    public function edit($id)
    {
        try {
            $subjectSector = $this->subjectSectorRepository->requireById($id);
            return View::make('admin.subjectsectors.edit', ['subjectSector' => $subjectSector]);
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }
    }

    public function update($id)
    {
        try {
            $subjectSector = $this->subjectSectorRepository->requireById($id);
            $subjectSector->fill(Input::all());

            if (!$subjectSector->isValid()) {
                return Redirect::back()->withInput()->withErrors($subjectSector->getErrors());
            }

            $subjectSector->save();

            return Redirect::route('admin.subjectsectors.index')
                ->with('successMessage', 'Updated subject sector successfully');
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }
    }

    public function deleteConfirm($id)
    {
        try {
            $subjectSector = $this->subjectSectorRepository->requireById($id);

            if (Request::ajax()) {
                return View::make('admin.subjectsectors.delete.modal',
                    ['subjectSector' => $subjectSector]);
            }

            return View::make('admin.subjectsectors.delete.fallback',
                ['subjectSector' => $subjectSector]);
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }
    }

    public function destroy($id)
    {
        try {
            $subjectSector = $this->subjectSectorRepository->requireById($id);
            $subjectSector->delete();
        } catch(ModelNotFoundException $e) {
            App::abort(404);
            return false;
        } catch (QueryException $e) {
            return Redirect::route('admin.subjectsectors.index')
                ->with('errorMessage', 'Unable to delete subject sector');
        }

        return Redirect::route('admin.subjectsectors.index')
            ->with('successMessage', 'Deleted subject sector');
    }

}