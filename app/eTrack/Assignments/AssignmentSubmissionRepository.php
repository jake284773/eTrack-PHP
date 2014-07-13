<?php namespace eTrack\Assignments;

use eTrack\Core\EloquentRepository;

class AssignmentSubmissionRepository extends EloquentRepository {

    public function __construct(AssignmentSubmission $model)
    {
        $this->model = $model;
    }

} 