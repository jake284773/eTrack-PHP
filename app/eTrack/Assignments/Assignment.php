<?php namespace eTrack\Assignments;

use DateTime;
use DB;
use eTrack\Core\Entity;
use eTrack\Courses\Criteria;
use eTrack\Courses\Unit;

/**
 * Assignment model
 *
 * @property string $id
 * @property-read Unit $unit
 * @property string $unit_id
 * @property string $brief
 * @property string $status
 * @property DateTime $available_date
 * @property DateTime $deadline
 * @property DateTime $marking_start_date
 * @property DateTime $marking_deadline
 * @property-read \Illuminate\Database\Eloquent\Collection|Criteria[] $criteria
 * @property-read \Illuminate\Database\Eloquent\Collection|AssignmentSubmission[] $submissions
 */
class Assignment extends Entity
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'assignment';

    protected $fillable = [
        'id',
        'unit_id',
        'number',
        'name',
        'available_date',
        'deadline',
        'marking_start_date',
        'marking_deadline',
    ];

    public function unit()
    {
        return $this->belongsTo('eTrack\Courses\Unit');
    }

    public function criteria()
    {
        return $this->belongsToMany('eTrack\Courses\Criteria')
            ->join(DB::raw('assignment_criteria ac2'), function ($join) {
                $join->on('criteria.id', '=', DB::raw('ac2.criteria_id'))
                    ->on('criteria.unit_id', '=', DB::raw('ac2.criteria_unit_id'));
            })
            ->orderBy(DB::raw('left(ac2.criteria_id, 1)'), 'desc')
            ->orderBy(DB::raw('left(ac2.criteria_id, 2)'))
            ->where(DB::raw('ac2.assignment_id'), $this->id);
    }

    public function submissions()
    {
        return $this->hasMany('eTrack\Assignments\AssignmentSubmission');
    }

}