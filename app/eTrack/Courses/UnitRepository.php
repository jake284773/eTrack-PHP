<?php namespace eTrack\Courses;

use eTrack\Core\EloquentRepository;

class UnitRepository extends EloquentRepository
{

    protected $subjectSectorRepository;

    public function __construct(Unit $model)
    {
        $this->model = $model;
    }

    protected function queryBySubjectAndSearch($search, $subjectSector)
    {
        $searchString = '%' . $search . '%';

        return $this->model->select('unit.id as id', 'number', 'unit.name as name',
            'credit_value', 'glh', 'level', 'subject_sector.name as subject_sector_name')
            ->join('subject_sector', 'unit.subject_sector_id', '=', 'subject_sector.id')
            ->orderBy('subject_sector.name')
            ->orderBy('unit.number')
            ->where('subject_sector_id', 'LIKE', $subjectSector)
            ->where(function ($query) use ($searchString) {
                $query->where('unit.id', 'LIKE', $searchString)
                    ->orWhere('unit.number', 'LIKE', $searchString)
                    ->orWhere('unit.name', 'LIKE', $searchString);
            });
    }

    public function getBySubjectAndSearch($search, $subjectSector)
    {
        return $this->queryBySubjectAndSearch($search, $subjectSector)->get();
    }

    public function getPaginatedBySubjectAndSearch($search, $subjectSector, $count = 15)
    {
        return $this->queryBySubjectAndSearch($search, $subjectSector)->paginate($count);
    }

    public function getWithRelated($id)
    {
        return $this->model->with([
                'criteria' => function ($query) {
                        $query->orderBy('type', 'desc')->orderBy('id', 'asc');
                    },
                'subject_sector', 'courses', 'courses.course_organiser']
        )->findOrFail($id);
    }

    public function getWithSubjectSector($id)
    {
        return $this->model->with('subject_sector')->findOrFail($id);
    }

    public function criteriaCount($id, $type = 'All')
    {
        $validTypes = ['All', 'Pass', 'Merit', 'Distinction'];

        if (! in_array($type, $validTypes))
            throw new \InvalidArgumentException();

        if ($type = 'All') {
            return $this->model->find($id)->criteria()->get()->count();
        }

        return $this->model->find($id)->criteria()->where('type', $type)->get()->count();
    }

} 