<?php namespace eTrack\Courses;

use DB;
use eTrack\Core\EloquentRepository;
use eTrack\Tracker\CourseStudentStructure;
use eTrack\Tracker\GradeStructure;
use eTrack\Tracker\StudentStructure;
use eTrack\Tracker\StudentUnitStructure;
use Illuminate\Database\Eloquent\Collection;

class CourseRepository extends EloquentRepository
{

    public function __construct(Course $model)
    {
        $this->model = $model;
    }

    protected function queryAllRelated()
    {
        return $this->model->with('subject_sector', 'faculty', 'course_organiser');
    }

    public function getAllRelated()
    {
        return $this->queryAllRelated()->all();
    }

    public function paginatedAllRelated($count = 15)
    {
        return $this->queryAllRelated()->paginate($count);
    }

    public function getForSubmission($id)
    {
        return $this->model->with('students', 'units', 'units.assignments')
            ->where('id', $id)->firstOrFail();
    }

    public function getWithRelated($id)
    {
        return $this->model->with('subject_sector', 'faculty', 'course_organiser',
            'units', 'students', 'student_groups', 'student_groups.tutor')
            ->where('id', $id)->firstOrFail();
    }

    public function getWithStudentsAndUnits($id)
    {
        return $this->model->with('students', 'units')
            ->where('id', $id)->firstOrFail();
    }

    public function getTrackerRelated($id)
    {
        return $this->model->with('units', 'units.studentGrades', 'students', 'student_groups')
            ->where('id', $id)->firstOrFail();
    }

    public function getAssessmentRelated($id, $assignmentId)
    {
        return $this->model
            ->with([
                'units', 'units.assignments', 'units.assignments.submissions',
                'units.assignments.criteria' => function ($query) use ($assignmentId) {
                    $query->join(DB::raw('assignment_criteria ac2'), function ($join) {
                        $join->on('criteria.id', '=', DB::raw('ac2.criteria_id'))
                            ->on('criteria.unit_id', '=', DB::raw('ac2.criteria_unit_id'));
                    });
                    $query->where(DB::raw('ac2.assignment_id'), $assignmentId);
                }
           ])->firstOrFail();
    }

    public function studentListForSelect($id, $course = null)
    {
        if (! $course) {
            $course = $this->model->with('students')->where('id', $id)->firstOrFail();
        }

        $studentList = [];

        foreach ($course->students as $student)
        {
            $studentList[$student->id] = $student->full_name . ' ('.$student->id.')';
        }

        return $studentList;
    }

    public function renderCourseUnitGradesForTracker(Course $course, $group = null)
    {
        $results = new Collection();

        if ($group) {
            $students = $course->students()
                ->leftJoin('student_group_student', 'student_group_student.student_user_id', '=', 'course_student.student_user_id')
                ->where('student_group_student.student_group_id', $group)
                ->get();
        } else {
            $students = $course->students;
        }

        foreach ($students as $student) {
            $units = new Collection();

            foreach ($course->units as $unit) {
                // Find the correct unit grade for the student.
                $unitGrade = $unit->studentGrades->filter(function ($unitGrade) use ($student) {
                    if ($unitGrade->student_user_id == $student->id) {
                        return true;
                    }

                    return false;
                })->first();

                // If the unit grade can't be found then add NYA to the array for
                // the unit grade.
                if (! $unitGrade) {
                    $unitGrade = new GradeStructure('NYA');
                } // Otherwise add the unit grade to the array.
                else {
                    $unitGrade = new GradeStructure($unitGrade->grade);
                }

                $unit = new StudentUnitStructure($unit, $unitGrade);

                unset($unitGrade);
                $units->add($unit);
                unset($unit);
            }

            $studentStructure = new StudentStructure($student);

            $finalGrade = new GradeStructure($student->pivot->final_grade);
            $predictedGrade = new GradeStructure($student->pivot->predicted_grade);
            $targetGrade = new GradeStructure($student->pivot->target_grade);

            $finalUcas = $student->pivot->final_ucas_tariff_score;
            $predictedUcas = $student->pivot->predicted_ucas_tariff_score;

            $studentResult = new CourseStudentStructure($studentStructure, $units,
                $finalGrade, $predictedGrade, $targetGrade,
                $finalUcas, $predictedUcas);

            unset($studentStructure);
            unset($units);
            unset($finalGrade);
            unset($predictedGrade);
            unset($targetGrade);
            unset($finalUcas);
            unset($predictedUcas);

            $results->add($studentResult);

            unset($studentResult);
        }

        return $results;
    }
}