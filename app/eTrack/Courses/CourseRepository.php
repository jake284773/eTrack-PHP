<?php namespace eTrack\Courses;

use eTrack\Core\EloquentRepository;

class CourseRepository extends EloquentRepository {

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

    public function getWithRelated($id)
    {
        return $this->model->with('subject_sector', 'faculty', 'course_organiser',
            'units', 'students', 'student_groups', 'student_groups.tutor')
            ->where('id', $id)->firstOrFail();
    }

    public function getTrackerRelated($id)
    {
        return $this->model->with('units', 'units.studentGrades', 'students')
            ->where('id', $id)->firstOrFail();
    }

    public function renderCourseUnitGradesForTracker(Course $course)
    {
        $results = [];

        foreach ($course->students as $student) {
            $studentInitial = substr($student->full_name, 0, 1);
            $studentNameSplit = explode(' ', $student->full_name);
            $studentLastName = strtoupper(array_pop($studentNameSplit));
            $studentName = $studentInitial.'. '.$studentLastName;

            foreach ($course->units as $unit) {
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
                    $results[$studentName][] = 'NYA';
                }
                // Otherwise add the unit grade to the array.
                else {
                    $results[$studentName][] = $unitGrade->grade;
                }
            }

            $results[$studentName][] = $student->pivot->final_grade;
            $results[$studentName][] = $student->pivot->predicted_grade;
            $results[$studentName][] = $student->pivot->target_grade;
            $results[$studentName][] = $student->pivot->final_ucas_tariff_score;
            $results[$studentName][] = $student->pivot->predicted_ucas_tariff_score;
        }

        return $results;
    }

} 