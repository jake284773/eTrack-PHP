<?php namespace eTrack\Controllers\Admin;

use App;
use eTrack\Controllers\BaseController;
use eTrack\Faculties\FacultyRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use View;
use Request;
use Redirect;
use Input;
use Illuminate\Database\QueryException;

class FacultyController extends BaseController
{

    /**
     * Faculty repository instance
     *
     * @var \eTrack\Faculties\FacultyRepository
     */
    protected $facultyRepository;

    public function __construct(FacultyRepository $facultyRepository)
    {
        $this->facultyRepository = $facultyRepository;
    }

    public function index()
    {
        $faculties = $this->facultyRepository->all();

        return View::make('admin.faculties.index', ['faculties' => $faculties]);
    }

    public function create()
    {
        return View::make('admin.faculties.create');
    }

    public function store()
    {
        $faculty = $this->facultyRepository->newInstance(Input::all());

        if (!$faculty->isValid()) {
            return Redirect::back()->withInput()->withErrors($faculty->getErrors());
        }

        $faculty->save();

        return Redirect::route('admin.faculties.index')
            ->with('successMessage', 'Created new faculty');
    }

    public function edit($id)
    {
        $faculty = $this->facultyRepository->find($id);

        if (!$faculty) App::abort(404);

        return View::make('admin.faculties.edit', ['faculty' => $faculty]);
    }


    public function show($id)
    {
        try {
            $faculty = $this->facultyRepository->getWithRelated($id);
            return View::make('admin.faculties.show', ['faculty' => $faculty]);
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }
    }

    public function update($id)
    {
        $faculty = $this->facultyRepository->find($id);
        $faculty->fill(Input::all());

        if (!$faculty->isValid()) {
            return Redirect::back()->withInput()->withErrors($faculty->getErrors());
        }

        $faculty->save();

        return Redirect::route('admin.faculties.index')
            ->with('successMessage', 'Updated faculty successfully');
    }

    public function deleteConfirm($id)
    {
        $faculty = $this->facultyRepository->find($id);

        if (Request::ajax()) {
            return View::make('admin.faculties.delete.modal', ['faculty' => $faculty]);
        }

        return View::make('admin.faculties.delete.fallback', ['faculty' => $faculty]);
    }

    public function destroy($id)
    {
        try {
            $faculty = $this->facultyRepository->find($id);
            $faculty->delete();
        } catch (QueryException $ex) {
            return Redirect::route('admin.faculties.index')
                ->with('errorMessage', 'Unable to delete faculty');
        }

        return Redirect::route('admin.faculties.index')
            ->with('successMessage', 'Deleted faculty');
    }

}