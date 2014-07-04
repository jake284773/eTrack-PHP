<?php namespace eTrack\Courses;

use DB;
use eTrack\Core\Entity;

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
class Criteria extends Entity
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

    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function studentAssessments()
    {
        return $this->hasMany('eTrack\Assessment\StudentAssessment', 'criteria_id')
            ->orderBy(DB::raw('left(`criteria_id`, 1)'), 'desc')
            ->orderBy(DB::raw('left(`criteria_id`, 2)'));
    }
}