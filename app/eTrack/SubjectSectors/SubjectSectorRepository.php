<?php namespace eTrack\SubjectSectors;

use eTrack\Core\EloquentRepository;

class SubjectSectorRepository extends EloquentRepository {

    public function __construct(SubjectSector $model)
    {
        $this->model = $model;
    }

    public function getWithRelated($id)
    {
        return $this->model->with('courses', 'courses.course_organiser', 'units')
            ->where('id', $id)->firstOrFail();
    }

    public function getAllOrdered()
    {
        return $this->model->orderBy('name')->get()->all();
    }

    public function getAllWithUnits()
    {
        return $this->model->join('unit', 'unit.subject_sector_id', '=', 'subject_sector.id')
            ->select('subject_sector.id', 'subject_sector.name')
            ->groupBy('subject_sector.name')
            ->get();
    }

} 