<?php namespace eTrack\Assessment;

use eTrack\Accounts\Student;
use eTrack\Accounts\User;
use eTrack\Assignments\Assignment;
use eTrack\Core\Entity;

/**
 * Student Assessment
 *
 * @property string $student_assignment_assignment_id
 * @property string $student_assignment_student_user_id
 * @property string $criteria_id
 * @property string $criteria_unit_id
 * @property string $assessor_user_id
 * @property string $moderator_user_id
 * @property string $assessment_status
 * @property string $last_updated
 * @property-read Assignment $assignment
 * @property-read Student $student
 * @property-read User $assessor
 * @property-read User $moderator
 * @method static StudentAssessment compositeKey($studentUserId, $criteriaId, $criteriaUnitId)
 */
class StudentAssessment extends Entity
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'criteria_student_assessment';

    protected $statuses = [
        'NYA'  => 'Not yet submitted',
        'AM'   => 'Submitted awaiting marking',
        'ALM'  => 'Submitted, awaiting overdue marking',
        'A'    => 'Achieved',
        'L'    => 'Late, not yet submitted',
        'LA'   => 'Late, submitted awaiting marking',
        'R1'   => 'Referral 1',
        'R1AM' => 'Referral 1 resubmitted, awaiting marking',
        'R2'   => 'Referral 2',
        'R2AM' => 'Referral 2 resubmitted, awaiting marking',
        'R3'   => 'Referral 3',
        'R3AM' => 'Referral 3 resubmitted, awaiting marking',
    ];

    public function scopeCompositeKey($query, $studentUserId, $criteriaId, $criteriaUnitId)
    {
        return $query
            ->where('student_assignment_student_user_id', $studentUserId)
            ->where('criteria_id', $criteriaId)
            ->where('criteria_unit_id', $criteriaUnitId);
    }

    public function criteria()
    {
        return $this->hasOne('eTrack\Courses\Criteria', 'id', 'criteria_id')
            ->where('unit_id', $this->criteria_unit_id);
    }

    public function submission()
    {
        return $this->belongsTo('eTrack\Assignments\AssignmentSubmission');
    }

    public function assignment()
    {
        return $this->belongsTo('eTrack\Assignments\Assignment', 'student_assignment_assignment_id');
    }

    public function student()
    {
        return $this->belongsTo('eTrack\Accounts\Student', 'student_assignment_student_user_id');
    }

    public function assessor()
    {
        return $this->belongsTo('eTrack\Accounts\User', 'assessor_user_id');
    }

    public function moderator()
    {
        return $this->belongsTo('eTrack\Accounts\User', 'moderator_user_id');
    }

}