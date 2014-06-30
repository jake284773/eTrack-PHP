<?php namespace eTrack\Courses;

use eTrack\Core\EloquentRepository;

class StudentAssessmentRepository extends EloquentRepository {

    public function __construct(StudentAssessment $model)
    {
        $this->model = $model;
    }

    public function getAllByType($type, $studentId, $unitId)
    {
        $validTypes = ['Pass' => 'P', 'Merit' => 'M', 'Distinction' => 'D'];

        if (! in_array($type, $validTypes)) {
            throw new \InvalidArgumentException("Invalid criteria type specified.");
        }

        return $this->model
            ->where('criteria_unit_id', $unitId)
            ->where('student_assignment_student_user_id', $studentId)
            ->whereRaw('left(`criteria_id`, 1) = ?', [$validTypes[$type]])
            ->get();
    }

} 