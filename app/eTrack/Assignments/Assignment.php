<?php namespace eTrack\Assignments;

use DateTime;
use DB;
use eTrack\Core\Entity;
use eTrack\Courses\Criteria;
use eTrack\Courses\Unit;

/**
 * Assignment model
 *
 * @property string                                                               $id
 * @property-read Unit                                                            $unit
 * @property string                                                               $unit_id
 * @property string                                                               $brief
 * @property string                                                               $status
 * @property DateTime                                                             $available_date
 * @property DateTime                                                             $deadline
 * @property DateTime                                                             $marking_start_date
 * @property DateTime                                                             $marking_deadline
 * @property-read \Illuminate\Database\Eloquent\Collection|Criteria[]             $criteria
 * @property-read \Illuminate\Database\Eloquent\Collection|AssignmentSubmission[] $submissions
 */
class Assignment extends Entity {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'assignment';

    protected $fillable = [
        'id',
        'number',
        'name',
        'available_date',
        'deadline',
        'marking_start_date',
        'marking_deadline',
    ];

    protected $validationRules = [
        'id'                 => 'required|unique:assignment,id,<id>|max:15',
        'number'             => 'required|integer',
        'name'               => 'required|max:150',
        'available_date'     => 'required|date',
        'deadline'           => 'required|date',
        'marking_start_date' => 'required|date',
        'marking_deadline'   => 'required|date',
    ];

    /**
     * Add some handlers to certain events
     */
    public static function boot()
    {
        parent::boot();

        // Hash the password and remove the password_confirmation attribute
        // when saving the record to the database.
        static::saving(function (Assignment $assignment)
        {
            if ($assignment->isValid())
            {
                return true;
            }

            return true;
        });
    }

    public function unit()
    {
        return $this->belongsTo('eTrack\Courses\Unit');
    }

    public function criteria()
    {
        return $this->belongsToMany('eTrack\Courses\Criteria', 'assignment_criteria', 'assignment_id')
            ->orderBy(DB::raw('left(ac2.criteria_id, 1)'), 'desc')
            ->orderBy(DB::raw('left(ac2.criteria_id, 2)'));
    }

    public function submissions()
    {
        return $this->belongsToMany('eTrack\Accounts\Student', 'student_assignment',
            'assignment_id', 'student_user_id')
            ->withPivot('submission_date', 'special_deadline');
    }

    public function assessments()
    {
        return $this->hasMany('eTrack\Assessment\StudentAssessment',
            'student_assignment_assignment_id', 'id')
            ->orderBy(DB::raw('left(criteria_id, 1)'), 'desc')
            ->orderBy(DB::raw('left(criteria_id, 2)'));
    }

    public function getDates()
    {
        return [
            'available_date',
            'deadline',
            'marking_start_date',
            'marking_deadline'
        ];
    }

}