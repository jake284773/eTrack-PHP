<?php namespace eTrack\Controllers\Admin;

use App;
use DB;
use eTrack\Controllers\BaseController;
use eTrack\SubjectSectors\SubjectSectorRepository;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use View;
use Request;
use Redirect;
use Input;

/**
 * Controller class for managing and viewing subject sectors on the system.
 *
 * Specialised for the Administrator role.
 *
 * @package eTrack\Controllers\Admin
 */
class SubjectSectorController extends BaseController {

    /**
     * Subject sector repository
     *
     * @var \eTrack\SubjectSectors\SubjectSectorRepository
     */
    protected $subjectSectorRepository;

    /**
     * Constructor for injecting the subject sector repository class.
     *
     * @param SubjectSectorRepository $subjectSectorRepository
     */
    public function __construct(SubjectSectorRepository $subjectSectorRepository)
    {
        $this->subjectSectorRepository = $subjectSectorRepository;
    }

    /**
     * Index view for showing all the subject sectors on the system.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Retrieve all the subject sectors from the database
        $subjectSectors = $this->subjectSectorRepository->all();

        // Render the index view with the retrieved data
        return View::make('admin.subjectsectors.index', [
            'subjectSectors' => $subjectSectors
        ]);
    }

    /**
     * Show form for creating a new subject sector
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return View::make('admin.subjectsectors.create');
    }

    /**
     * Store a new subject sector record into the database from the form data
     * submitted with the view from the create method.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        // Store the accepted form data into an array
        $formData = [
            'id' => Input::get('subject_sector_id'),
            'name' => Input::get('subject_sector_name')
        ];

        // Get a new instance of a subject sector model
        $subjectSector = $this->subjectSectorRepository->newInstance();

        // Store the ID and name values from the form into the model
        $subjectSector->id = $formData['id'];
        $subjectSector->name = $formData['name'];

        // Validate the model
        // If this fails redirect the user back to the form with the validation
        // errors.
        if (! $subjectSector->isValid()) {
            return Redirect::back()->withInput()->withErrors($subjectSector->getErrors());
        }

        // Validation was successful so try to insert the record into the database.
        // If any error occurs then the changes will be reversed and the user
        // will be notified that it cannot be created.
        try {
            DB::transaction(function() use($subjectSector) {
                $subjectSector->save();
            });
        } catch (Exception $e) {
            return Redirect::back()->withInput()
                ->with('errorMessage', 'Unable to create new subject sector');
        }

        // The record was inserted successfully, so redirect the user back to the
        // subject sector list with a success message.
        return Redirect::route('admin.subjectsectors.index')
            ->with('successMessage', 'Created new subject sector');
    }

    /**
     * View for displaying a specific subject sector.
     *
     * Also shows related courses and units
     *
     * @param $id
     * @return bool|\Illuminate\View\View
     */
    public function show($id)
    {
        // Try to find subject sector record
        // If it can't be found return a 404 error.
        try {
            $subjectSector = $this->subjectSectorRepository->findEagerLoaded($id, [
                'courses', 'courses.course_organiser', 'units'
            ]);
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }

        // Render view with subject sector record
        return View::make('admin.subjectsectors.show', [
            'subjectSector' => $subjectSector
        ]);
    }

    public function edit($id)
    {
        // Try to find subject sector record
        // If it can't be found return a 404 error.
        try {
            $subjectSector = $this->subjectSectorRepository->find($id);
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }

        // Render edit form with the form values filled in with the existing
        // record.
        return View::make('admin.subjectsectors.edit', [
            'subjectSector' => $subjectSector
        ]);
    }

    public function update($id)
    {
        // Store the accepted form data into an array
        $formData = [
            'name' => Input::get('subject_sector_name')
        ];

        // Try to find subject sector record
        // If it can't be found return a 404 error.
        try {
            $subjectSector = $this->subjectSectorRepository->find($id);
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }

        // Set the name value from the form into the model instance.
        $subjectSector->name = $formData['name'];

        // Validate the model
        // If validation fails redirect the user back with the validation errors
        // included.
        if (!$subjectSector->isValid()) {
            return Redirect::back()->withInput()->withErrors($subjectSector->getErrors());
        }

        // Validation was successful so try to update the record in the database.
        // If any error occurs then the changes will be reversed and the user
        // will be notified that it cannot be updated.
        try {
            DB::transaction(function() use($subjectSector) {
                $subjectSector->save();
            });
        } catch (Exception $e) {
            return Redirect::back()->withInput()->with('errorMessage', 'Unable to update subject sector.');
        }

        // The changes were saved successfully, so redirect the user back to the
        // subject sector list with a success message.
        return Redirect::route('admin.subjectsectors.index')
            ->with('successMessage', 'Updated subject sector successfully');
    }

    public function deleteConfirm($id)
    {
        // Try to find subject sector record
        // If it can't be found return a 404 error.
        try {
            $subjectSector = $this->subjectSectorRepository->find($id);
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }

        // If the request is made via AJAX then show the modal view
        if (Request::ajax()) {
            return View::make('admin.subjectsectors.delete.modal',
                ['subjectSector' => $subjectSector]);
        }

        // Otherwise render the normal view
        return View::make('admin.subjectsectors.delete.fallback',
            ['subjectSector' => $subjectSector]);
    }

    public function destroy($id)
    {
        // Try to find subject sector record
        // If it can't be found return a 404 error.
        try {
            $subjectSector = $this->subjectSectorRepository->find($id);
        } catch(ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }

        // Try to delete the subject sector record
        // If any error occurs the changes will be rolled back and the user
        // will be notified that the subject sector cannot be deleted.
        try {
            DB::transaction(function() use($subjectSector) {
                $subjectSector->delete();
            });
        } catch (Exception $e) {
            return Redirect::route('admin.subjectsectors.index')
                ->with('errorMessage', 'Unable to delete subject sector');
        }

        // The subject sector was deleted successfully so redirect back to the
        // subject sector list and show a success message.
        return Redirect::route('admin.subjectsectors.index')
            ->with('successMessage', 'Deleted subject sector');
    }

}