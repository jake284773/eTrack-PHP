<?php namespace eTrack\Courses;

use eTrack\Core\EloquentRepository;

class StudentGroupRepository extends EloquentRepository {

    public function __construct(StudentGroup $model)
    {
        $this->model = $model;
    }

    public function getWithRelated($id)
    {
        return $this->model->with('course', 'students', 'tutor')
            ->where('id', $id)->firstOrFail();
    }

} 