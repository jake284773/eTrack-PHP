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
        'number',
        'name',
        'available_date_string',
        'available_hour',
        'available_minute',
        'deadline_date_string',
        'deadline_hour',
        'deadline_minute',
        'marking_start_date_string',
        'marking_start_hour',
        'marking_start_minute',
        'marking_deadline_date_string',
        'marking_deadline_hour',
        'marking_deadline_minute',
    ];

    protected $validationRules = [
        'id'                           => 'required|unique:assignment,id,<id>|max:15',
        'number'                       => 'required|integer',
        'name'                         => 'required|max:150',
        'available_date_string'        => 'required|date_format:d/m/Y',
        'available_hour'               => 'required|date_format:H',
        'available_minute'             => 'required|date_format:i',
        'deadline_date_string'         => 'required|date_format:d/m/Y',
        'deadline_hour'                => 'required|date_format:H',
        'deadline_minute'              => 'required|date_format:i',
        'marking_start_date_string'    => 'required|date_format:d/m/Y',
        'marking_start_hour'           => 'required|date_format:H',
        'marking_start_minute'         => 'required|date_format:i',
        'marking_deadline_date_string' => 'required|date_format:d/m/Y',
        'marking_deadline_hour'        => 'required|date_format:H',
        'marking_deadline_minute'      => 'required|date_format:i',
    ];

    /**
     * Add some handlers to certain events
     */
    public static function boot()
    {
        parent::boot();

        // Hash the password and remove the password_confirmation attribute
        // when saving the record to the database.
        static::saving(function (Assignment $assignment) {
            if ($assignment->isValid()) {
                $assignment->saveFragmentedDate('available_date');
                $assignment->saveFragmentedDate('deadline');
                $assignment->saveFragmentedDate('marking_start_date');
                $assignment->saveFragmentedDate('marking_deadline');

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

    public function getAvailableDateStringAttribute()
    {
        $date = new DateTime($this->attributes['available_date']);
        return $date->format('d/m/Y');
    }

    public function getAvailableHourAttribute()
    {
        $date = new DateTime($this->attributes['available_date']);
        return $date->format('H');
    }

    public function getAvailableMinuteAttribute()
    {
        $date = new DateTime($this->attributes['available_date']);
        return $date->format('i');
    }

    public function getDeadlineDateStringAttribute()
    {
        $date = new DateTime($this->attributes['deadline']);
        return $date->format('d/m/Y');
    }

    public function getDeadlineHourAttribute()
    {
        $date = new DateTime($this->attributes['deadline']);
        return $date->format('H');
    }

    public function getDeadlineMinuteAttribute()
    {
        $date = new DateTime($this->attributes['deadline']);
        return $date->format('i');
    }

    public function getMarkingStartDateStringAttribute()
    {
        $date = new DateTime($this->attributes['marking_start_date']);
        return $date->format('d/m/Y');
    }

    public function getMarkingStartHourAttribute()
    {
        $date = new DateTime($this->attributes['marking_start_date']);
        return $date->format('H');
    }

    public function getMarkingStartMinuteAttribute()
    {
        $date = new DateTime($this->attributes['marking_start_date']);
        return $date->format('i');
    }

    public function getMarkingDeadlineDateStringAttribute()
    {
        $date = new DateTime($this->attributes['marking_deadline']);
        return $date->format('d/m/Y');
    }

    public function getMarkingDeadlineHourAttribute()
    {
        $date = new DateTime($this->attributes['marking_deadline']);
        return $date->format('H');
    }

    public function getMarkingDeadlineMinuteAttribute()
    {
        $date = new DateTime($this->attributes['marking_deadline']);
        return $date->format('i');
    }

    private function produceDate($date, $hour, $minute)
    {
        $dateSplit = explode('/', $date);
        $dateString = $dateSplit[2] . '-' . $dateSplit[1] . '-' . $dateSplit[0] . ' ' . $hour . ':' . $minute . ':00';

        $dateTime = new DateTime($dateString);
        return $dateTime->format('Y-m-d H:i:s');
    }

    private function saveFragmentedDate($type, $clearFragments = true)
    {
        $validTypes = [
            'available_date', 'deadline', 'marking_start_date',
            'marking_deadline'
        ];

        if (!in_array($type, $validTypes)) {
            throw new \InvalidArgumentException('Invalid date fragment type');
        }

        if (strstr($type, '_date')) {
            $typeSplit = explode('_date', $type);
            $shortType = $typeSplit[0];
        } else {
            $shortType = $type;
        }

        $this->attributes[$type] = $this->produceDate(
            $this->attributes[$shortType . '_date_string'],
            $this->attributes[$shortType . '_hour'],
            $this->attributes[$shortType . '_minute']
        );

        if ($clearFragments) {
            unset($this->attributes[$shortType . '_date_string']);
            unset($this->attributes[$shortType . '_hour']);
            unset($this->attributes[$shortType . '_minute']);
        }
    }

}