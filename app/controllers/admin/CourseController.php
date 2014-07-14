<?php namespace eTrack\Controllers\Admin;

use App;
use eTrack\Controllers\BaseController;
use eTrack\Courses\CourseRepository;
use eTrack\Courses\UnitRepository;
use eTrack\SubjectSectors\SubjectSectorRepository;
use eTrack\Faculties\FacultyRepository;
use eTrack\Accounts\UserRepository;
use eTrack\Courses\Course;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use View;
use Input;
use Redirect;

class CourseController extends BaseController {

    protected $courseRepository;
    protected $subjectSectorRepository;
    protected $facultyRepository;
    protected $userRepository;

    public function __construct(CourseRepository $courseRepository,
                                UnitRepository $unitRepository,
                                SubjectSectorRepository $subjectSectorRepository,
                                FacultyRepository $facultyRepository,
                                UserRepository $userRepository)
    {
        $this->courseRepository = $courseRepository;
        $this->unitRepository = $unitRepository;
        $this->subjectSectorRepository = $subjectSectorRepository;
        $this->facultyRepository = $facultyRepository;
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        $courses = $this->courseRepository->paginatedAllRelated();

        return View::make('admin.courses.index', ['courses' => $courses]);
    }

    public function create()
    {
        $subjectSectors = $this->subjectSectorRepository->getAllOrdered();
        $subjectSectorsForm = ['' => ''];

        foreach ($subjectSectors as $subjectSector) {
            $subjectSectorsForm[(string) $subjectSector->id] = $subjectSector->name;
        }

        $faculties = $this->facultyRepository->getAllOrdered();
        $facultiesForm = ['' => ''];

        foreach ($faculties as $faculty) {
            $facultiesForm[$faculty->id] = $faculty->name;
        }

        $courseOrganisers = $this->userRepository->getByRole('Course Organiser');
        $courseOrganisersForm = ['' => ''];

        foreach ($courseOrganisers as $courseOrganiser) {
            $courseOrganisersForm[$courseOrganiser->id] = $courseOrganiser->full_name;
        }

        $validCourseTypes = Course::$validTypes;
        $validCourseTypesForm = ['' => ''];

        foreach ($validCourseTypes as $validCourseType) {
            $validCourseTypesForm[$validCourseType] = $validCourseType;
        }

        $students = $this->userRepository->getByRole('Student');
        $studentsForm = ['' => ''];

        foreach ($students as $student)
        {
          $studentsForm[$student->id] = $student->full_name . ' (' . $student->id . ')';
        }

        $units = $this->unitRepository->getAllWithSubjectSector();
        $unitsForm = ['' => ''];

        foreach ($units as $unit)
        {
          $unitsForm[$unit->subject_sector->name][$unit->id] = $unit->full_name;
        }


        return View::make('admin.courses.create', [
            'subjectSectors' => $subjectSectorsForm,
            'faculties' => $facultiesForm,
            'courseOrganisers' => $courseOrganisersForm,
            'validCourseTypes' => $validCourseTypesForm,
            'students' => $studentsForm,
            'units' => $unitsForm,
        ]);
    }

    public function store()
    {
        $formData = Input::all();

        $course = $this->courseRepository->getNew($formData);
        
        if (! $course->isValid()) {
            return Redirect::back()->withInput()->withErrors($course->getErrors());
        }

        $units = $this->unitRepository->getAllWithSubjectSector();

        $selectedUnits = [];

        foreach ($formData['units'] as $unit)
        {
            $unitRecord = $units->filter(function($unitRecord) {
                if ($unitRecord->id == $unit) {
                    return true;
                }

                return false;
            })->find(0);

            $selectedUnits[$unit] = ['unit_number' => $unitRecord->number];
        }

        try {
            DB::transaction(function () use ($course, $formData) {
                $course->save();
                $course->units()->sync($selectedUnits);
                $course->students()->sync($formData['students']);
            });
        } catch (\Exception $e) {
            return Redirect::back()
                ->withInput()
                ->with('errorMessage', 'Unable to create new course.');
        }

        return Redirect::route('admin.courses.index')
            ->with('successMessage', 'Created new course');
    }

    public function show($id)
    {
        try {
            $course = $this->courseRepository->getWithRelated($id);

            return View::make('admin.courses.show', ['course' => $course]);
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }
    }

    public function edit($id)
    {

    }

    public function update($id)
    {

    }

    public function deleteConfirm($id)
    {

    }

    public function destroy($id)
    {

    }

} 