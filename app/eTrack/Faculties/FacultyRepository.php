<?php namespace eTrack\Faculties;

use eTrack\Core\EloquentRepository;

class FacultyRepository extends EloquentRepository {

    public function __construct(Faculty $model)
    {
        $this->model = $model;
    }

    public function getWithRelated($id)
    {
        return $this->model->with('courses', 'courses.course_organiser')
            ->where('id', '=', $id)->firstOrFail();
    }

} 