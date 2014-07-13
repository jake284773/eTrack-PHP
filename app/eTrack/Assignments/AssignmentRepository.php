<?php namespace eTrack\Assignments;

use DB;
use eTrack\Core\EloquentRepository;

class AssignmentRepository extends EloquentRepository
{

    public function __construct(Assignment $model)
    {
        $this->model = $model;
    }

    public function getWithCriteria($id)
    {
        return $this->model->with(['criteria' => function ($query) use ($id) {
                $query->join(DB::raw('assignment_criteria ac2'), function ($join) {
                    $join->on('criteria.id', '=', DB::raw('ac2.criteria_id'))
                        ->on('criteria.unit_id', '=', DB::raw('ac2.criteria_unit_id'));
                });
                $query->where(DB::raw('ac2.assignment_id'), $id);
            }])->find($id);
    }

    public function getWithSubmissionsAndCriteria($id)
    {
        return $this->model->with([
            'criteria' => function ($query) use ($id) {
                    $query->join(DB::raw('assignment_criteria ac2'), function ($join) {
                        $join->on('criteria.id', '=', DB::raw('ac2.criteria_id'))
                            ->on('criteria.unit_id', '=', DB::raw('ac2.criteria_unit_id'));
                    });
                    $query->where(DB::raw('ac2.assignment_id'), $id);
                },
            'submissions', 'submissions.student'
        ])->find($id);
    }

} 