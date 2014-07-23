<?php namespace eTrack\Core;

use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\Paginator;

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

    /**
     * Create a new model instance with the specified attributes filled in.
     *
     * @param array $attributes
     *
     * @return Entity
     */
    public function newInstance(array $attributes = [])
    {
        return $this->model->newInstance($attributes);
    }

    /**
     * Retrieve all records for the model.
     *
     * @return Collection|Entity[]
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * Retrieve a paginator object containing a paginated result of all records.
     *
     * @param int   $perPage Number of records per page
     * @param array $columns Columns to select
     *
     * @return Paginator
     */
    public function paginate($perPage = 15, $columns = ['*'])
    {
        return $this->model->paginate($perPage, $columns);
    }

    /**
     * @param string $id      Model primary key
     * @param array  $columns Columns to select
     *
     * @return Entity
     */
    public function find($id, $columns = ['*'])
    {
        return $this->model->findOrFail($id, $columns);
    }

    /**
     * Delete a record by it's primary key
     *
     * @param string $id Record primary key
     *
     * @return int
     */
    public function destroy($id)
    {
        return $this->model->destroy($id);
    }

} 