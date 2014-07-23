<?php namespace eTrack\SubjectSectors;

use eTrack\Core\EloquentRepository;

/**
 * Subject sector repository class
 *
 * Abstraction layer for database operations
 *
 * @package eTrack\SubjectSectors
 */
class SubjectSectorRepository extends EloquentRepository {

    public function __construct(SubjectSector $model)
    {
        $this->model = $model;
    }

    /**
     * Finds a record with the specified relations eager loaded.
     *
     * @param float $id ID to find
     * @param array $relations The relations to eager load
     * @param array $columns Specify which columns to retrieve
     * @return \Illuminate\Database\Eloquent\Collection|SubjectSector[]|SubjectSector
     */
    public function findEagerLoaded($id, array $relations, $columns = ['*'])
    {
        return $this->model->with($relations)
            ->findOrFail($id, $columns);
    }

    /**
     * Retrieve all records ordered by the name field ascending.
     *
     * @return \Illuminate\Database\Eloquent\Collection|SubjectSector[]
     */
    public function allOrderByName()
    {
        return $this->model->nameAscending()->get();
    }

    /**
     * Retrieves all subject sectors that have units.
     *
     * @param bool $orderByName True to order records by name ascending
     * @return \Illuminate\Database\Eloquent\Collection|SubjectSector[]
     */
    public function allWithUnits($orderByName = true)
    {
        if ($orderByName) {
            return $this->model->nameAscending()->has('units')->get();
        }

        return $this->model->has('units')->get();
    }

} 