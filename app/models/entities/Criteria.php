<?php namespace eTrack\Models\Entities;

/**
 * Unit criteria model
 *
 * @property string $id
 * @property Unit $unit The unit which this criteria belongs to
 * @property string $type Type of criteria (Pass, Merit, Distinction)
 * @property string $unit_id
 * @method static Criteria compositeKey($id, $unitId)
 * @author Jake Moreman <mail@jakemoreman.co.uk>
 */
class Criteria extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'criteria';

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