<?php namespace eTrack\Controllers\Admin;

use SubjectSector;
use View;
use Request;
use Redirect;
use Illuminate\Database\QueryException;

class SubjectSectorController extends \BaseController {

    public function index()
    {
        $subjectSectors = SubjectSector::all();

        return View::make('admin.subjectsectors.index', array('subjectSectors' => $subjectSectors));
    }

    public function create()
    {
        
    }

    public function show($id)
    {
        
    }

    public function edit($id)
    {
        
    }

    public function deleteConfirm($id)
    {
        
    }

    public function destroy($id)
    {
        
    }

}