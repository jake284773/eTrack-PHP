<?php namespace eTrack\Courses;

use DB;
use eTrack\Accounts\Student;
use eTrack\Core\EloquentRepository;
use eTrack\Tracker\CriteriaAssessmentStructure;
use eTrack\Tracker\GradeStructure;
use eTrack\Tracker\StudentStructure;
use eTrack\Tracker\StudentUnitStructure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UnitRepository extends EloquentRepository
{
    protected $criteriaModel;

    public function __construct(Unit $model, Criteria $criteriaModel)
    {
        $this->model = $model;
        $this->criteriaModel = $criteriaModel;
    }

    public function getAllWithSubjectSector()
    {
        return $this->model->select('unit.*')
            ->with('subject_sector')
            ->join('subject_sector', 'unit.subject_sector_id', '=', 'subject_sector.id')
            ->orderBy('subject_sector.name')
            ->orderBy('unit.number')
            ->get();
    }

    public function getAllNotInCourse($courseId)
    {
        return $this->model->select('unit.*')
            ->with('subject_sector')
            ->join('subject_sector', 'unit.subject_sector_id', '=', 'subject_sector.id')
            ->leftJoin('course_unit', function($join) use($courseId)
            {
                $join->on('unit.id', '=', 'course_unit.unit_id')
                    ->where('course_unit.course_id', '=', $courseId);
            })
            ->where('course_unit.unit_id')
            ->orderBy('subject_sector.name')
            ->orderBy('unit.number')
            ->get();
    }

    protected function queryBySubjectAndSearch($search, $subjectSector)
    {
        $searchString = '%' . $search . '%';

        return $this->model->select('unit.id as id', 'number', 'unit.name as name',
            'credit_value', 'glh', 'level', 'subject_sector.name as subject_sector_name')
            ->join('subject_sector', 'unit.subject_sector_id', '=', 'subject_sector.id')
            ->orderBy('subject_sector.name')
            ->orderBy('unit.number')
            ->where('subject_sector_id', 'LIKE', $subjectSector)
            ->where(function ($query) use ($searchString) {
                $query->where('unit.id', 'LIKE', $searchString)
                    ->orWhere('unit.number', 'LIKE', $searchString)
                    ->orWhere('unit.name', 'LIKE', $searchString);
            });
    }

    public function getBySubjectAndSearch($search, $subjectSector)
    {
        return $this->queryBySubjectAndSearch($search, $subjectSector)->get();
    }

    public function getPaginatedBySubjectAndSearch($search, $subjectSector, $count = 15)
    {
        return $this->queryBySubjectAndSearch($search, $subjectSector)->paginate($count);
    }

    public function getWithRelated($id)
    {
        return $this->model->with([
                'criteria' => function ($query) {
                        $query->orderBy('type', 'desc')->orderBy('id', 'asc');
                    },
                'subject_sector', 'courses', 'courses.course_organiser']
        )->findOrFail($id);
    }

    public function getWithCriteria($id, $type = null)
    {
        return $this->model->with([
                'criteria' => function ($query) use ($type) {
                        if ($type) {
                            $query->where('type', $type);
                        }
                        $query->orderBy('type', 'desc')->orderBy('id', 'asc');
                    }]
        )->findOrFail($id);
    }

    public function getWithCriteriaAndAssessments($id)
    {
        return $this->model->with([
            'studentGrades',
            'criteria'                    => function ($query) {
                    $query->orderBy('type', 'desc')->orderBy('id', 'asc');
                },
            'criteria.studentAssessments' => function ($query) use ($id) {
                    $query->where('criteria_unit_id', '=', $id);
                }
        ])->findOrFail($id);
    }

    public function getWithSubjectSector($id)
    {
        return $this->model->with('subject_sector')
          ->findOrFail($id);
    }

    public function criteriaListForSelect($id, $unit = null)
    {
        if (! $unit) {
            $unit = $this->getWithCriteria($id);
        }

        $criteriaSelect = [];

        foreach ($unit->criteria as $criteria)
        {
            $criteriaSelect[$criteria->id] = $criteria->id;
        }

        return $criteriaSelect;
    }

    /**
     * Calculates the total number of criteria that is part of a unit.
     *
     * Specifying a criteria type will filter by that specific type.
     *
     * @param Unit|string $unit Unit model object or Unit ID
     * @param string $type The type of criteria (pass, merit or distinction)
     *                     Must be in the format of P, M or D.
     * @return integer Number of criteria
     * @throws \InvalidArgumentException
     */
    public function getTotalCriteria($unit, $type = null)
    {
        if (is_string($unit)) {
            $unit = $this->getWithCriteriaAndAssessments($unit);
        }

        if ($type) {
            if (! in_array($type, ['P', 'M', 'D'])) {
                throw new \InvalidArgumentException('Invalid criteria type. Accepted: P, M or D.');
            }

            $passCriteria = $unit->criteria->filter(function ($criteria) use($type) {
                if (substr($criteria->id, 0, 1) == $type) {
                    return true;
                }

                return false;
            });
        } else {
            $passCriteria = $unit->criteria;
        }

        return $passCriteria->count();
    }

    /**
     * Produces an array which contains the criteria marks and unit grade for
     * all students on the specified course.
     *
     * This is used in the tracker by unit view.
     *
     * @param Course $course The course model object with the course_students
     *                       records eager loaded.
     * @param Unit $unit The unit model object to generate from.
     * @return array The results array
     */
    public function renderUnitCriteriaAssessmentForTracker(Course $course, Unit $unit)
    {
        $results = new Collection();

        foreach ($course->students as $student) {
            $criteriaResult = new Collection();

            foreach ($unit->criteria as $criteria) {
                // Find the assessment record for the correct student
                $criteriaResult->add($this->findAssessmentForStudent($criteria, $student));
            }

            // Find the correct unit grade for the student.
            $unitGrade = $this->findUnitGradeForStudent($unit, $student);

            $studentStructure = new StudentStructure($student);

            $results->add(new StudentUnitStructure($unit, $unitGrade, $criteriaResult, $studentStructure));

            unset($studentStructure);
            unset($unitGrade);
            unset($criteriaResult);
        }

        return $results;
    }

    /**
     * Checks whether the specified unit is part of the specified course.
     *
     * @param string $courseId The ID of the course to check.
     * @param string $unitId The ID of the unit to check.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return bool True if unit exists in course.
     */
    public function checkUnitBelongsToCourse($courseId, $unitId)
    {
        $unitCount = DB::table('course_unit')->where('course_id', $courseId)
            ->where('unit_id', $unitId)->count();

        if ($unitCount == 0) throw new ModelNotFoundException();

        return true;
    }

    /**
     * Retrieve the criteria assessment record for a particular student and
     * criterion, then instantiate a criteria assessment structure object from it.
     *
     * @param Criteria $criteria The criteria model object which represents the
     *                           criterion to find.
     * @param Student $student The student model object which represents which
     *                         student this search is for.
     * @return CriteriaAssessmentStructure
     */
    private function findAssessmentForStudent(Criteria $criteria, Student $student)
    {
        $assessment = $criteria->studentAssessments->filter(function ($assessment) use ($student, $criteria) {
            $assessmentStudentId = $assessment->student_assignment_student_user_id;
            $assessmentCriteriaId = $assessment->criteria_id;

            if ($assessmentStudentId == $student->id && $assessmentCriteriaId == $criteria->id) {
                return true;
            }

            return false;
        })->first();

        // If no assessment could be found, then assume the student has not yet
        // achieved it yet.
        if (! $assessment) {
            $assessmentStructure = new CriteriaAssessmentStructure($criteria->id, 'NYA');
        }
        // Otherwise add the found criterion assessment status to the structure object.
        else {
            $assessmentStructure = new CriteriaAssessmentStructure($criteria->id,
                $assessment->assessment_status);
        }

        return $assessmentStructure;
    }

    /**
     * Retrieve the student unit record for a particular unit and student,
     * then instantiate a grade structure object from it.
     *
     * @param Unit $unit
     * @param Student $student
     * @return GradeStructure
     */
    private function findUnitGradeForStudent(Unit $unit, Student $student)
    {
        $unitGrade = $unit->studentGrades->filter(function ($unitGrade) use ($student) {
            if ($unitGrade->student_user_id == $student->id) {
                return true;
            }

            return false;
        })->first();

        // If the unit grade can't be found then set it to NYA.
        if (! $unitGrade) {
            $unitGradeStructure = new GradeStructure('NYA');
        }
        // Otherwise add the unit grade to the new grade structure object.
        else {
            $unitGradeStructure = new GradeStructure($unitGrade->grade);
        }

        return $unitGradeStructure;
    }

    public function getWithAssignments($id)
    {
        return $this->model->with([
            'criteria'                    => function ($query) {
                    $query->orderBy('type', 'desc')->orderBy('id', 'asc');
                },
            'assignments',
            'subject_sector'
        ])->findOrFail($id);
    }

} 