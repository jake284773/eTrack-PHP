<?php

/**
 * Unit criteria model
 *
 * @property string $id
 * @property Unit $unit The unit which this criteria belongs to
 * @property string $type Type of criteria (Pass, Merit, Distinction)
 *
 * @author Jake Moreman <mail@jakemoreman.co.uk>
 */
class Criteria extends Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'criteria';

    /**
     * Disable timestamp management.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Retrieve a criteria record with a composite primary key.
     *
     * This is used instead of the standard find method as it doesn't support
     * the use of composite primary keys.
     *
     * @param $query
     * @param string $id The criteria id
     * @param string $unitId The unit id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompositeKey($query, $id, $unitId)
    {
        return $query
            ->where('id', $id)
            ->where('unit_id', $unitId);
    }
}