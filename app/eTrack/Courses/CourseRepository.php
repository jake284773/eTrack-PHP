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

} 