<?php namespace eTrack\Courses;

class StudentUnitGradeCalc {

    protected $studentAssessmentRepository;
    protected $unitRepository;
    protected $totalCriteria = [];
    protected $studentAssessments = [];

    private $achievedCount = ['Pass', 'Merit', 'Distinction'];

    public function __construct(UnitRepository $unitRepository,
                                StudentAssessmentRepository $studentAssessmentRepository)
    {
        $this->unitRepository = $unitRepository;
        $this->studentAssessmentRepository = $studentAssessmentRepository;
    }

    private function getTotalAchieved($type, $studentId, $unitId)
    {
        $validTypes = ['P', 'M', 'D'];

        if (! in_array($type, $validTypes)) {
            throw new \InvalidArgumentException("Invalid criteria type specified.");
        }

        if (! isset($this->studentAssessments[$unitId])) {
            $this->studentAssessments[$unitId] = $this->studentAssessmentRepository->getAllByUnit($unitId);
        }

        $studentAssessments = $this->studentAssessments[$unitId]->filter(function($assessment) use($studentId, $type)
        {
            if ($assessment->student_assignment_student_user_id == $studentId &&
                substr($assessment->criteria_id, 0, 1) == $type) {
                return true;
            }

            return false;
        });
        
        $totalAchieved = 0;

        foreach ($studentAssessments as $studentAssessment) {
            if ($studentAssessment->assessment_status == 'A') {
                $totalAchieved++;
            }
        }
        
        $this->achievedCount[$type] = $totalAchieved;
    }

    public function calcUnitGrade($studentId, $unitId)
    {
        $this->getTotalAchieved('P', $studentId, $unitId);
        $this->getTotalAchieved('M', $studentId, $unitId);
        $this->getTotalAchieved('D', $studentId, $unitId);

        if (! isset($this->totalCriteria[$unitId])) {
            $unitCriteria = Criteria::where('unit_id', $unitId)->get();

            $this->totalCriteria[$unitId] = [
                'P' => $unitCriteria->filter(function($criteria)
                    {
                        if (substr($criteria->id, 0, 1) == 'P') {
                            return true;
                        }

                        return false;
                    })->count(),
                'M' => $unitCriteria->filter(function($criteria)
                    {
                        if (substr($criteria->id, 0, 1) == 'M') {
                            return true;
                        }

                        return false;
                    })->count(),
                'D' => $unitCriteria->filter(function($criteria)
                    {
                        if (substr($criteria->id, 0, 1) == 'D') {
                            return true;
                        }

                        return false;
                    })->count(),
            ];
        }
        
        if ($this->achievedCount['D'] == $this->totalCriteria[$unitId]['D'] &&
            $this->achievedCount['M'] == $this->totalCriteria[$unitId]['M'] &&
            $this->achievedCount['P'] == $this->totalCriteria[$unitId]['P']) {
            return 'Distinction';
        } elseif ($this->achievedCount['M'] == $this->totalCriteria[$unitId]['M'] &&
            $this->achievedCount['P'] == $this->totalCriteria[$unitId]['P']) {
            return 'Merit';
        } elseif ($this->achievedCount['P'] == $this->totalCriteria[$unitId]['P']) {
            return 'Pass';
        } else {
            return 'NYA';
        }
    }

} 