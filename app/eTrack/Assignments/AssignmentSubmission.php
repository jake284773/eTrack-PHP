<?php namespace eTrack\Assignments;

use DateTime;
use eTrack\Accounts\Student;
use eTrack\Core\Entity;

/**
 * Assignment submission model
 *
 * @property-read Assignment $assignment
 * @property-read Student $student
 * @property DateTime $special_deadline
 * @property DateTime $submission_date
 * @property string $assignment_id
 * @property string $student_user_id
 * @method static AssignmentSubmission compositeKey($assignmentId, $studentUserId)
 */
class AssignmentSubmission extends Entity
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'student_assignment';

    protected $validationRules = [
        'assignment_id'                => 'required|exists:assignment,id',
        'student_user_id'              => 'required|exists:user,id,role,Student',
        'submission_date_string'       => 'required|date_format:d/m/Y',
        'submission_hour'              => 'required|date_format:H',
        'submission_minute'            => 'required|date_format:i',
        'special_deadline_date_string' => 'required_if:special_deadline_required,yes|date_format:d/m/Y',
        'special_deadline_hour'        => 'required_if:special_deadline_required,yes|date_format:H',
        'special_deadline_minute'      => 'required_if:special_deadline_required,yes|date_format:i',
    ];

    protected $validationAttributeNames = [
        'student_user_id'              => 'student',
        'submission_date_string'       => 'submission date',
        'special_deadline_date_string' => 'special deadline date',
    ];

    protected $fillable = [
        'assignment_id',
        'student_user_id',
        'submission_date_string',
        'submission_hour',
        'submission_minute',
        'special_deadline_required',
        'special_deadline_date_string',
        'special_deadline_hour',
        'special_deadline_minute',
    ];

    /**
     * Add some handlers to certain events
     */
    public static function boot()
    {
        parent::boot();

        // Hash the password and remove the password_confirmation attribute
        // when saving the record to the database.
        static::saving(function (AssignmentSubmission $submission) {
            if ($submission->isValid()) {
                $submission->saveFragmentedDate('submission_date');

                if (isset($submission->attributes['special_deadline_required'])) {
                    $submission->saveFragmentedDate('special_deadline');
                    unset($submission->attributes['special_deadline_required']);
                }

                return true;
            }

            return true;
        });
    }

    /**
     * Retrieve a assignment submission record with the composite primary key.
     *
     * This is used instead of the standard find method as it doesn't support
     * the use of composite primary keys.
     *
     * @param $query
     * @param string $assignmentId The assignment ID
     * @param string $studentUserId The student's user ID
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompositeKey($query, $assignmentId, $studentUserId)
    {
        return $query
            ->where('assignment_id', $assignmentId)
            ->where('student_user_id', $studentUserId);
    }

    public function student()
    {
        return $this->hasOne('eTrack\Accounts\Student', 'id', 'student_user_id');
    }

    public function criteriaAssessments()
    {
        return $this->hasMany('eTrack\Assessment\StudentAssessment',
            'student_assignment_assignment_id', 'assignment_id')
            ->where('student_assignment_student_user_id', $this->student_user_id);
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
        $validTypes = ['submission_date', 'special_deadline'];

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