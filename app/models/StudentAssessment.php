<?php

class StudentAssessment extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'criteria_student_assessment';

    protected $statuses = array(
        'NYA' => 'Not yet submitted',
        'AM' => 'Submitted awaiting marking',
        'ALM' => 'Submitted, awaiting overdue marking',
        'A' => 'Achieved',
        'L' => 'Late, not yet submitted',
        'LA' => 'Late, submitted awaiting marking',
        'R1' => 'Referral 1',
        'R1AM' => 'Referral 1 resubmitted, awaiting marking',
        'R2' => 'Referral 2',
        'R2AM' => 'Referral 2 resubmitted, awaiting marking',
        'R3' => 'Referral 3',
        'R3AM' => 'Referral 3 resubmitted, awaiting marking',
    );

    public function scopeCompositeKey($query, $studentUserId, $criteriaId, $criteriaUnitId)
    {
        return $query
            ->where('student_assignment_student_user_id', $studentUserId)
            ->where('criteria_id', $criteriaId)
            ->where('criteria_unit_id', $criteriaUnitId);
    }

    public function assignment()
    {
        return $this->belongsTo('Assignment', 'student_assignment_assignment_id');
    }

    public function student()
    {
        return $this->belongsTo('Student', 'student_assignment_student_user_id');
    }

    public function assessor()
    {
        return $this->belongsTo('User', 'assessor_user_id');
    }

    public function moderator()
    {
        return $this->belongsTo('User', 'moderator_user_id');
    }

}