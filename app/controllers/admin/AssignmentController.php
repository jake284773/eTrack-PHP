<?php namespace eTrack\Controllers\Admin;

use App;
use Carbon\Carbon;
use DateTime;
use DB;
use eTrack\Assessment\StudentAssessmentRepository;
use eTrack\Assignments\AssignmentRepository;
use eTrack\Controllers\BaseController;
use eTrack\Courses\CourseRepository;
use eTrack\Courses\UnitRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Redirect;
use Input;
use Request;
use Validator;
use View;

class AssignmentController extends BaseController {

    protected $courseRepository;

    protected $unitRepository;

    protected $assignmentRepository;

    protected $studentAssessmentRepository;

    public function __construct(CourseRepository $courseRepository,
                                UnitRepository $unitRepository,
                                AssignmentRepository $assignmentRepository,
                                StudentAssessmentRepository $studentAssessmentRepository)
    {
        $this->courseRepository = $courseRepository;
        $this->unitRepository = $unitRepository;
        $this->assignmentRepository = $assignmentRepository;
        $this->studentAssessmentRepository = $studentAssessmentRepository;
    }

    public function show($courseId, $unitId, $assignmentId)
    {
        try
        {
            $course = $this->courseRepository->getWithStudentsAndUnits($courseId);
            $unit = $this->unitRepository->getWithCriteria($unitId);
            $assignment = $this->assignmentRepository->getWithSubmissionsAndCriteria($assignmentId);
        }
        catch (ModelNotFoundException $e)
        {
            App::abort(404);

            return false;
        }

        return View::make('admin.courses.units.assignments.show', [
            'course'     => $course,
            'unit'       => $unit,
            'assignment' => $assignment,
        ]);
    }

    public function create($courseId, $unitId)
    {
        try
        {
            $course = $this->courseRepository->getWithStudentsAndUnits($courseId);
            $unit = $this->unitRepository->getWithCriteria($unitId);
            $selectCriteria = $this->unitRepository->criteriaListForSelect($unitId, $unit);
        }
        catch (ModelNotFoundException $e)
        {
            App::abort(404);

            return false;
        }

        return View::make('admin.courses.units.assignments.create', [
            'course'         => $course,
            'unit'           => $unit,
            'selectCriteria' => $selectCriteria,
        ]);
    }

    public function store($courseId, $unitId)
    {
        try
        {
            $course = $this->courseRepository->getWithStudentsAndUnits($courseId);
            $unit = $this->unitRepository->getWithCriteria($unitId);
        }
        catch (ModelNotFoundException $e)
        {
            App::abort(404);

            return false;
        }

        $criteriaValidation = Validator::make(
            Input::only('criteria'),
            ['criteria' => 'required|exists:criteria,id,unit_id,' . $unitId]
        );

        if ($criteriaValidation->fails())
        {
            return Redirect::back()->withInput()
                ->withErrors($criteriaValidation->errors());
        }

        $assignmentModelData = [
            'id'                 => Input::get('assignment_id'),
            'number'             => Input::get('assignment_number'),
            'name'               => Input::get('assignment_name'),
            'available_date'     => $this->createDateTime(
                    Input::get('available_date'),
                    Input::get('available_hour'),
                    Input::get('available_minute')
                ),
            'deadline'           => $this->createDateTime(
                    Input::get('deadline_date'),
                    Input::get('deadline_hour'),
                    Input::get('deadline_minute')
                ),
            'marking_start_date' => $this->createDateTime(
                    Input::get('marking_start_date'),
                    Input::get('marking_start_hour'),
                    Input::get('marking_start_minute')
                ),
            'marking_deadline'   => $this->createDateTime(
                    Input::get('marking_deadline_date'),
                    Input::get('marking_deadline_hour'),
                    Input::get('marking_deadline_minute')
                ),
        ];

        $assignment = $this->assignmentRepository->newInstance($assignmentModelData);
        $assignment->unit_id = $unitId;

        if (! $assignment->isValid())
        {
            return Redirect::back()->withInput()->withErrors($assignment->getErrors());
        }

        $criteriaRecords = [];

        foreach (Input::get('criteria') as $criteria)
        {
            $criteriaRecords[$criteria] = ['criteria_unit_id' => $assignment->unit_id];
        }

        try
        {
            DB::transaction(function () use ($assignment, $criteriaRecords)
            {
                $assignment->save();
                $assignment->criteria()->sync($criteriaRecords);
            });
        }
        catch (\Exception $e)
        {
            return Redirect::back()->withInput()->with('errorMessage',
                'Unable to save new assignment to database.');
        }

        return Redirect::route('admin.courses.units.show', [$course->id, $unit->id])
            ->with('successMessage', 'Created new assignment.');
    }

    public function edit($courseId, $unitId, $assignmentId)
    {
        try
        {
            $course = $this->courseRepository->getWithStudentsAndUnits($courseId);
            $unit = $this->unitRepository->getWithCriteria($unitId);
            $selectCriteria = $this->unitRepository->criteriaListForSelect($unitId, $unit);
            $assignment = $this->assignmentRepository->getWithCriteria($assignmentId);
        }
        catch (ModelNotFoundException $e)
        {
            App::abort(404);

            return false;
        }

        return View::make('admin.courses.units.assignments.edit', [
            'course'         => $course,
            'unit'           => $unit,
            'selectCriteria' => $selectCriteria,
            'assignment'     => $assignment,
        ]);
    }

    public function update($courseId, $unitId, $assignmentId)
    {
        try
        {
            $course = $this->courseRepository->getWithStudentsAndUnits($courseId);
            $unit = $this->unitRepository->getWithCriteria($unitId);
            $assignment = $this->assignmentRepository->getWithCriteria($assignmentId);
        }
        catch (ModelNotFoundException $e)
        {
            App::abort(404);

            return false;
        }

        $assignment->fill(Input::all());

        if (! $assignment->isValid())
        {
            return Redirect::back()->withInput()->withErrors($assignment->getErrors());
        }

        $criteriaRecords = [];

        foreach (Input::get('criteria') as $criteria)
        {
            $criteriaRecords[$criteria] = ['criteria_unit_id' => $assignment->unit_id];
        }

        try
        {
            DB::transaction(function () use ($assignment, $criteriaRecords)
            {
                $assignment->save();
                $assignment->criteria()->sync($criteriaRecords);
            });
        }
        catch (\Exception $e)
        {
            return Redirect::back()->withInput()->with('errorMessage',
                'Unable to save changes to the database.');
        }

        return Redirect::route('admin.courses.units.assignments.show', [$course->id, $unit->id, $assignment->id])
            ->with('successMessage', 'Updated assignment');
    }

    public function deleteConfirm($courseId, $unitId, $assignmentId)
    {
        try
        {
            $course = $this->courseRepository->getWithStudentsAndUnits($courseId);
            $unit = $this->unitRepository->getWithCriteria($unitId);
            $assignment = $this->assignmentRepository->getWithCriteria($assignmentId);
        }
        catch (ModelNotFoundException $e)
        {
            App::abort(404);

            return false;
        }

        if (Request::ajax())
        {
            return View::make('admin.courses.units.assignments.delete.modal', [
                'course' => $course, 'unit' => $unit, 'assignment' => $assignment
            ]);
        }

        return View::make('admin.courses.units.assignments.delete.fallback', [
            'course' => $course, 'unit' => $unit, 'assignment' => $assignment
        ]);
    }

    public function destroy($courseId, $unitId, $assignmentId)
    {
        try
        {
            $course = $this->courseRepository->getWithStudentsAndUnits($courseId);
            $unit = $this->unitRepository->getWithCriteria($unitId);
            $assignment = $this->assignmentRepository->getWithSubmissionsAndCriteria($assignmentId);
        }
        catch (ModelNotFoundException $e)
        {
            App::abort(404);

            return false;
        }

        try
        {
            DB::transaction(function () use ($assignment)
            {
                $assignment->criteria()->detach();
                $assignment->assessments()->delete();
                $assignment->submissions()->detach();
                $assignment->delete();
            });
        }
        catch (\Exception $e)
        {
            return Redirect::route('admin.courses.units.show', [$course->id, $unit->id])
                ->with('errorMessage', 'Unable to delete assignment');
        }

        return Redirect::back()->with('successMessage', 'Deleted assignment');
    }

    /**
     * Produces a DateTime string for the specified date, hour and minute.
     *
     * @param string         $date The date string. Should be in the format (22/04/2014)
     * @param string|integer $hour
     * @param string|integer $minute
     *
     * @return string
     */
    private function createDateTime($date, $hour, $minute)
    {
        $dateString = $date . ' ' . $hour . ':' . $minute;

        return Carbon::createFromFormat('d/m/Y H:i', $dateString)->toDateTimeString();
    }

}