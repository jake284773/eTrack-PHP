<?php namespace eTrack\Controllers\Admin;

use App;
use DB;
use eTrack\Controllers\BaseController;
use eTrack\Courses\Criteria;
use eTrack\Courses\UnitRepository;
use eTrack\SubjectSectors\SubjectSectorRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use View;
use Request;
use Redirect;
use Input;

class UnitController extends BaseController
{

    /**
     * @var \eTrack\Repositories\UnitRepository
     */
    protected $unitRepository;

    protected $subjectSectorRepository;

    public function __construct(UnitRepository $unitRepository, SubjectSectorRepository $subjectSectorRepository)
    {
        $this->unitRepository = $unitRepository;
        $this->subjectSectorRepository = $subjectSectorRepository;
    }

    public function index()
    {
        $units = $this->unitRepository->getPaginatedBySubjectAndSearch(Input::get('search'),
            Input::get('subjectsector'));

        $subjectSectors = $this->subjectSectorRepository->getAllWithUnits();
        $subjectSectorsForm = ['' => 'All subject sectors'];

        foreach ($subjectSectors as $subjectSector) {
            $subjectSectorsForm[(string)$subjectSector->id] = $subjectSector->name;
        }

        return View::make('admin.units.index', [
            'units' => $units,
            'subjectSectorsForm' => $subjectSectorsForm
        ]);
    }

    public function create()
    {
        $subjectSectors = $this->subjectSectorRepository->getAllOrdered();
        $subjectSectorsForm = ['' => ''];

        foreach ($subjectSectors as $subjectSector) {
            $subjectSectorsForm[(string)$subjectSector->id] = $subjectSector->name;
        }

        return View::make('admin.units.create', ['subjectSectorsForm' => $subjectSectorsForm]);
    }

    public function store()
    {
        $unit = $this->unitRepository->getNew(Input::all());

        if (! $unit->isValid())
        {
            return Redirect::back()->withInput()->withErrors($unit->getErrors());
        }

        $passCriteria = $this->generateCriteria($unit->number_of_pass_criteria, 'Pass');
        $meritCriteria = $this->generateCriteria($unit->number_of_merit_criteria, 'Merit');
        $distinctionCriteria = $this->generateCriteria($unit->number_of_distinction_criteria, 'Distinction');

        try {
            DB::transaction(function () use ($unit, $passCriteria, $meritCriteria, $distinctionCriteria) {
                $unit->save();

                $unit->criteria()->saveMany($passCriteria);
                $unit->criteria()->saveMany($meritCriteria);
                $unit->criteria()->saveMany($distinctionCriteria);
            });
        } catch (\Exception $e) {
            return Redirect::back()
                ->withInput()
                ->with('errorMessage', 'Unable to save new unit. If the problem persists contact IT services.');
        }

        return Redirect::route('admin.units.index')
            ->with('successMessage', 'Created new unit');
    }

    public function show($id)
    {
        try {
            $unit = $this->unitRepository->getWithRelated($id);
            return View::make('admin.units.show', ['unit' => $unit]);
        } catch (\Exception $e) {
            App::abort(404);
            return false;
        }
    }

    public function deleteConfirm($id)
    {
        try {
            $unit = $this->unitRepository->getWithSubjectSector($id);

            if (Request::ajax()) {
                return View::make('admin.units.delete.modal', ['unit' => $unit]);
            }

            return View::make('admin.units.delete.fallback', ['unit' => $unit]);
        } catch (\Exception $e) {
            App::abort(404);
            return false;
        }
    }

    public function destroy($id)
    {
        try {
            $unit = $this->unitRepository->getById($id);
            $unit->delete();
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        } catch (\Exception $e) {
            return Redirect::route('admin.units.index')
                ->withInput()
                ->with('errorMessage', 'Unable to delete unit');
        }

        return Redirect::route('admin.units.index')
            ->withInput()
            ->with('successMessage', 'Deleted unit');
    }

    private function generateCriteria($number, $type)
    {
        $validTypes = ['Pass', 'Merit', 'Distinction'];

        if (! in_array($type, $validTypes)) {
            throw new \InvalidArgumentException();
        }

        $criteriaArray = [];

        for ($i = 1; $i <= $number; $i++) {
            $criteria = new Criteria();
            $criteria->id = substr($type, 0, 1) . $i;
            $criteria->type = $type;

            $criteriaArray[] = $criteria;
        }

        return $criteriaArray;
    }

}

