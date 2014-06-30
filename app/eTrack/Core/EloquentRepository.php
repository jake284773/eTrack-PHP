<?php namespace eTrack\Core;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use eTrack\Core\Exceptions\EntityNotFoundException;
use Symfony\Component\Process\Exception\InvalidArgumentException;

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

    public function setModel($model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function getAllPaginated($count = 15)
    {
        return $this->model->paginate($count);
    }

    public function getById($id)
    {
        return $this->model->find($id);
    }

    public function requireById($id)
    {
        $model = $this->getById($id);

        if (! $model) {
            throw new EntityNotFoundException;
        }

        return $model;
    }

    public function create($attributes = array())
    {
        return $this->model->create($attributes);
    }

    public function getNew($attributes = array())
    {
        return $this->model->newInstance($attributes);
    }

    public function save($data)
    {
        if ($data instanceof Model) {
            return $this->storeEloquentModel($data);
        } elseif (is_array($data)) {
            return $this->storeArray($data);
        } else {
            throw new InvalidArgumentException;
        }
    }

    public function delete(Eloquent $model)
    {
        return $model->delete();
    }

    protected function storeEloquentModel(Model $model)
    {
        if ($model->getDirty()) {
            return $model->save();
        } else {
            return $model->touch();
        }
    }

    protected function storeArray($data)
    {
        $model = $this->getNew($data);
        return $this->storeEloquentModel($model);
    }

} 