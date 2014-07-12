<?php namespace eTrack\Controllers\Admin;

use App;
use DateTime;
use DB;
use eTrack\Assignments\AssignmentRepository;
use eTrack\Controllers\BaseController;
use eTrack\Courses\CourseRepository;
use eTrack\Courses\UnitRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Redirect;
use Input;
use Validator;
use View;

class AssignmentController extends BaseController
{

    protected $courseRepository;
    protected $unitRepository;
    protected $assignmentRepository;

    public function __construct(CourseRepository $courseRepository, UnitRepository $unitRepository,
                                AssignmentRepository $assignmentRepository)
    {
        $this->courseRepository = $courseRepository;
        $this->unitRepository = $unitRepository;
        $this->assignmentRepository = $assignmentRepository;
    }

    public function show($courseId, $unitId, $assignmentId)
    {
        try {
            $course = $this->courseRepository->getWithStudentsAndUnits($courseId);
            $unit = $this->unitRepository->getWithCriteria($unitId);
            $assignment = $this->assignmentRepository->getWithSubmissions($assignmentId);
        } catch (ModelNotFoundException $e) {
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
        try {
            $course = $this->courseRepository->getWithStudentsAndUnits($courseId);
            $unit = $this->unitRepository->getWithCriteria($unitId);
            $selectCriteria = $this->unitRepository->criteriaListForSelect($unitId, $unit);
            $studentList = $this->courseRepository->studentListForSelect($courseId, $course);
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }

        return View::make('admin.courses.units.assignments.create', [
            'course'         => $course,
            'unit'           => $unit,
            'selectCriteria' => $selectCriteria,
            'students'       => $studentList,
        ]);
    }

    public function store($courseId, $unitId)
    {
        try {
            $this->courseRepository->getWithStudentsAndUnits($courseId);
            $this->unitRepository->getWithCriteria($unitId);
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }

        $validationRules = [
            'id'                 => 'required|unique:assignment,id|max:15',
            'number'             => 'required|integer',
            'name'               => 'required|max:150',
            'available_date'     => 'required|after:yesterday|date',
            'deadline'           => 'required|after:yesterday|date',
            'marking_start_date' => 'required|after:yesterday|date',
            'marking_deadline'   => 'required|after:yesterday|date',
        ];

        $formData = [
            'id'                 => Input::get('id'),
            'unit_id'            => $unitId,
            'number'             => Input::get('number'),
            'name'               => Input::get('name'),
            'available_date'     => $this->produceDate(
                    Input::get('available_date'),
                    Input::get('available_hour'),
                    Input::get('available_minute')
                ),
            'deadline'           => $this->produceDate(
                    Input::get('deadline_date'),
                    Input::get('deadline_hour'),
                    Input::get('deadline_minute')
                ),
            'marking_start_date' => $this->produceDate(
                    Input::get('marking_start_date'),
                    Input::get('marking_start_hour'),
                    Input::get('marking_start_minute')
                ),
            'marking_deadline'   => $this->produceDate(
                    Input::get('marking_deadline_date'),
                    Input::get('marking_deadline_hour'),
                    Input::get('marking_deadline_minute')
                ),
        ];

        $validator = Validator::make($formData, $validationRules);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator->errors());
        }

        try {
            DB::transaction(function () use ($unitId, $formData) {
                $assignment = $this->assignmentRepository->getNew($formData);
                $assignment->save();

                foreach (Input::get('criteria') as $criteria) {
                    $assignment->criteria()->attach($assignment->id,
                        ['criteria_id' => $criteria, 'criteria_unit_id' => $unitId]);
                }
            });
        } catch (\Exception $e) {
            return Redirect::back()->withInput()->with('errorMessage', 'Unable to save new assignment to database.');
        }

        return Redirect::route('admin.courses.units.show', [$courseId, $unitId])
            ->with('successMessage', 'Created new assignment');
    }

    private function produceDate($date, $hour, $minute)
    {
        $dateSplit = explode('/', $date);
        $dateString = $dateSplit[2] . '-' . $dateSplit[1] . '-' . $dateSplit[0] . ' ' . $hour . ':' . $minute . ':00';

        $dateTime = new DateTime($dateString);
        return $dateTime->format('Y-m-d H:i:s');
    }

} 