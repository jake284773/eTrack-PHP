<?php namespace eTrack\Courses;

use DB;
use eTrack\Core\EloquentRepository;

class UnitRepository extends EloquentRepository
{
    protected $criteriaModel;

    protected $assessmentStatusStylingMap = [
        'NYA'  => 'criteria-nya',
        'AM'   => 'criteria-awaitmark',
        'ALM'  => 'criteria-awaitlatemark',
        'A'    => 'criteria-achieved',
        'L'    => 'criteria-late',
        'LA'   => 'criteria-achieved criteria-lateachieved',
        'R1'   => 'criteria-ref criteria-r1',
        'R1AM' => 'criteria-ref criteria-r1awaitmark',
        'R2'   => 'criteria-ref criteria-r2',
        'R2AM' => 'criteria-ref criteria-r2awaitmark',
        'R3'   => 'criteria-ref criteria-r3',
        'R3AM' => 'criteria-ref criteria-r3awaitmark',
    ];

    public function __construct(Unit $model, Criteria $criteriaModel)
    {
        $this->model = $model;
        $this->criteriaModel = $criteriaModel;
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
        return $this->model->with('subject_sector')->findOrFail($id);
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
        $results = [];

        foreach ($course->students as $student) {
            foreach ($unit->criteria as $criteria) {
                // Find the assessment record for the correct student
                $assessment = $criteria->studentAssessments->filter(function($assessment) use($student, $criteria)
                {
                    $assessmentStudentId = $assessment->student_assignment_student_user_id;
                    $assessmentCriteriaId = $assessment->criteria_id;

                    if ($assessmentStudentId == $student->id && $assessmentCriteriaId == $criteria->id) {
                        return true;
                    }

                    return false;
                })->first();

                // If no assessment could be found then add NYA to the results array
                // for that criterion and student
                if (! $assessment) {
                    $results[$student->full_name.' ('.$student->id.')'][] =
                        $this->assessmentStatusStylingMap['NYA'];
                }
                // Otherwise add the found criterion status code to the results array.
                else {
                    $results[$student->full_name.' ('.$student->id.')'][] =
                        $this->assessmentStatusStylingMap[$assessment->assessment_status];
                }
            }

            // Find the correct unit grade for the student.
            $unitGrade = $unit->studentGrades->filter(function($unitGrade) use($student)
            {
                if ($unitGrade->student_user_id == $student->id) {
                    return true;
                }

                return false;
            })->first();

            // If the unit grade can't be found then add NYA to the array for
            // the unit grade.
            if (! $unitGrade) {
                $results[$student->full_name.' ('.$student->id.')'][] = 'NYA';
            }
            // Otherwise add the unit grade to the array.
            else {
                $results[$student->full_name.' ('.$student->id.')'][] = $unitGrade->grade;
            }
        }

        return $results;
    }

    /**
     * Checks whether the specified unit is part of the specified course.
     *
     * @param string $courseId The ID of the course to check.
     * @param string $unitId The ID of the unit to check.
     * @return bool True if unit exists in course.
     */
    public function checkUnitBelongsToCourse($courseId, $unitId)
    {
        $unitCount = DB::table('course_unit')->where('course_id', $courseId)
            ->where('unit_id', $unitId)->count();

        if ($unitCount == 0) return false;

        return true;
    }

} 