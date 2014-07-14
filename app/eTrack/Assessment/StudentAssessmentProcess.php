<?php namespace eTrack\Assessment;

use DateTime;

/**
 * Class for processing a student assessment.
 *
 * Validates and checks the data submitted
 *
 */
class StudentAssessmentProcess {

    protected $validationRules = [
        'assessment_status' => 'required|'
    ];

    protected $statusCodes = [
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

    public function getValidationRules()
    {
        
    }
    
    public getPossibleAssessmentCodes($currentCode = null)
    {
        $timeNow = new DateTime();
        
        
        
        switch ($currentCode)
        {
            case ''
            
            
            default:
                return $this->statusCodes;
        }
    }

    public function validate($data)

}