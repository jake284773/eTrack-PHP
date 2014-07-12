<?php namespace eTrack\Assignments;

use eTrack\Core\EloquentRepository;

class AssignmentRepository extends EloquentRepository {

    public function __construct(Assignment $model)
    {
        $this->model = $model;
    }

    public function getWithSubmissions($id)
    {
        return $this->model->with('submissions', 'submissions.student')->find($id);
    }

} 