<?php namespace eTrack\Controllers\Admin;

use App;
use DB;
use eTrack\Controllers\BaseController;
use eTrack\Courses\CourseRepository;
use eTrack\Courses\UnitRepository;
use Event;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Input;
use Redirect;
use Request;
use Validator;
use View;

class CourseUnitController extends BaseController
{

    protected $unitRepository;
    protected $courseRepository;

    public function __construct(UnitRepository $unitRepository, CourseRepository $courseRepository)
    {
        parent::__construct();

        $this->unitRepository = $unitRepository;
        $this->courseRepository = $courseRepository;
    }

    public function add($courseId)
    {
        try {
            $course = $this->courseRepository->getById($courseId);
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }

        // If the user is trying to add a unit to a course which already
        // has enough units, return an error page.
        if ($course->units()->get()->count() >= $course::MAX_UNITS) {
            App::abort(404);
        }

        $units = $this->unitRepository->getAllNotInCourse($courseId);

        $unitSelect = ['' => ''];

        foreach ($units as $unit) {
            $unitSelect[$unit->subject_sector->name][$unit->id] = $unit->fullName;
        }

        return View::make('admin.courses.units.add', ['course' => $course, 'units' => $unitSelect]);
    }

    public function store($courseId)
    {
        try {
            $course = $this->courseRepository->getById($courseId);
            $unit = $this->unitRepository->getById(Input::get('unit'));
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }

        $validationRules = [
            'unit'        => 'required|exists:unit,id|unique:course_unit,unit_id,NULL,id,course_id,' . $courseId,
            'unit_number' => 'integer|unique:course_unit,unit_number,NULL,id,course_id,' . $courseId,
        ];

        $formData = [
            'unit'        => Input::get('unit'),
            'unit_number' => Input::get('unit-number') ? : $unit->number,
        ];

        $validation = Validator::make($formData, $validationRules);

        if ($validation->fails()) {
            return Redirect::back()->withInput()->withErrors($validation->errors());
        }

        try {
            DB::transaction(function() use($course, $formData) {
                $course->units()->attach($course->id, [
                    'unit_id'     => $formData['unit'],
                    'unit_number' => $formData['unit_number']
                ]);

                Event::fire('tracker.calcAllPredictedGrades', $course->id);
                Event::fire('tracker.calcAllFinalGrades', $course->id);
            });
        } catch (\Exception $e) {
            return Redirect::back()->withInput()->with('errorMessage', 'Unable to add unit to course');
        }

        return Redirect::route('admin.courses.show', [$courseId])->with('successMessage', 'Added unit to course.');
    }

    public function show($courseId, $unitId)
    {
        if (!$this->unitRepository->checkUnitBelongsToCourse($courseId, $unitId)) {
            App::abort(404);
        }

        $course = $this->courseRepository->getById($courseId);
        $unit = $this->unitRepository->getWithAssignments($unitId);

        return View::make('admin.courses.units.show', ['course' => $course, 'unit' => $unit]);
    }

    public function deleteConfirm($courseId, $unitId)
    {
        try {
            $course = $this->courseRepository->requireById($courseId);
            $unit = $course->units()->where('id', $unitId)->firstOrFail();

            $this->unitRepository->checkUnitBelongsToCourse($courseId, $unitId);

            if (Request::ajax()) {
                return View::make('admin.courses.units.delete.modal',
                    ['course' => $course, 'unit' => $unit]);
            }

            return View::make('admin.courses.units.delete.fallback',
                ['course' => $course, 'unit' => $unit]);
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }
    }

    public function destroy($courseId, $unitId)
    {
        try {
            $course = $this->courseRepository->requireById($courseId);

            $this->unitRepository->checkUnitBelongsToCourse($courseId, $unitId);

            DB::transaction(function () use ($course, $unitId) {
                $course->units()->detach($unitId);
                Event::fire('tracker.calcAllPredictedGrades', $course->id);
                Event::fire('tracker.calcAllFinalGrades', $course->id);
            });
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        } catch (\Exception $e) {
            return Redirect::back()->with('errorMessage', 'Unable to remove unit from course.');
        }

        return Redirect::route('admin.courses.show', [$course->id])
            ->with('successMessage', 'Removed unit from course');
    }

} 