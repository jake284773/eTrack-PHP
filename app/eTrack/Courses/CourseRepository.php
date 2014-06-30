<?php namespace eTrack\Courses;

use eTrack\Core\EloquentRepository;

class CourseRepository extends EloquentRepository {

    public function __construct(Course $model)
    {
        $this->model = $model;
    }

} 