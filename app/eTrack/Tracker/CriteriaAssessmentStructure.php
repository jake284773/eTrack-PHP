<?php namespace eTrack\Tracker;

use eTrack\Core\AbstractReadOnlyStructure;

class CriteriaAssessmentStructure extends AbstractReadOnlyStructure {
    protected $id;
    protected $status;
    protected $statusCssClass;

    protected $assessmentStatusStylingMap = [
        'NYA'  => 'criteria-nya',
        'AM'   => 'criteria-awaitmark',
        'ALM'  => 'criteria-awaitlatemark',
        'A'    => 'criteria-achieved',
        'L'    => 'criteria-late',
        'LA'   => 'criteria-achieved criteria-lateachieved',
        'R1'   => 'criteria-ref criteria-r1',
        'R1AM' => 'criteria-ref criteria-r1awaitmark',
        'R2'   => 'criteria-ref criteria-r2',
        'R2AM' => 'criteria-ref criteria-r2awaitmark',
        'R3'   => 'criteria-ref criteria-r3',
        'R3AM' => 'criteria-ref criteria-r3awaitmark',
    ];

    public function __construct($id, $status)
    {
        $this->id = $id;
        $this->status = $status;
        $this->statusCssClass = $this->assessmentStatusStylingMap[$status];
    }
} 