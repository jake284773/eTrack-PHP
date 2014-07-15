<?php namespace eTrack\Core;

use Eloquent;

abstract class EloquentRepository {

    protected $model;

    public function __construct(Eloquent $model = null)
    {
        $this->model = $model;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    public function newInstance(array $attributes = [])
    {
        return $this->model->newInstance($attributes);
    }

    public function all()
    {
        return $this->model->all();
    }

    public function paginate($perPage = 15, $columns = ['*'])
    {
        return $this->model->paginate($perPage, $columns);
    }

    public function find($id, $columns = ['*'])
    {
        return $this->model->findOrFail($id, $columns);
    }

    public function destroy($id)
    {
        return $this->model->destroy($id);
    }

} 