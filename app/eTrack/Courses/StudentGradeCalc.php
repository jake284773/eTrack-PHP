<?php namespace eTrack\Courses;

class StudentGradeCalc {

    protected $studentAssessmentRepository;
    protected $unitRepository;

    private $achievedCount = ['Pass', 'Merit', 'Distinction'];

    public function __construct(UnitRepository $unitRepository,
                                StudentAssessmentRepository $studentAssessmentRepository)
    {
        $this->unitRepository = $unitRepository;
        $this->studentAssessmentRepository = $studentAssessmentRepository;
    }

    private function getTotalAchieved($type, $studentId, $unitId)
    {
        $validTypes = ['Pass', 'Merit', 'Distinction'];

        if (! in_array($type, $validTypes)) {
            throw new \InvalidArgumentException("Invalid criteria type specified.");
        }

        $studentAssessments = $this->studentAssessmentRepository
            ->getAllByType($type, $studentId, $unitId);
        
        $totalAchieved = 0;

        foreach ($studentAssessments as $studentAssessment) {
            if ($studentAssessment->assessment_status) {
                $totalAchieved++;
            }
        }
        
        $this->achievedCount[$type] = $totalAchieved;
    }

    public function calcUnitGrade($studentId, $unitId)
    {
        $this->getTotalAchieved('Pass', $studentId, $unitId);
        $this->getTotalAchieved('Merit', $studentId, $unitId);
        $this->getTotalAchieved('Distinction', $studentId, $unitId);

        $totalDistinctionCriteria = $this->unitRepository->getById($unitId)
            ->criteria()->where('type', 'Distinction')->count();
        $totalMeritCriteria = $this->unitRepository->getById($unitId)
            ->criteria()->where('type', 'Merit')->count();;
        $totalPassCriteria = $this->unitRepository->getById($unitId)
            ->criteria()->where('type', 'Pass')->count();;
        
        if ($this->achievedCount['Distinction'] == $totalDistinctionCriteria &&
            $this->achievedCount['Merit'] == $totalMeritCriteria &&
            $this->achievedCount['Pass'] == $totalPassCriteria) {
            return 'Distinction';
        } elseif ($this->achievedCount['Merit'] == $totalMeritCriteria &&
            $this->achievedCount['Pass'] == $totalPassCriteria) {
            return 'Merit';
        } elseif ($this->achievedCount['Pass'] == $totalPassCriteria) {
            return 'Pass';
        } else {
            return 'NYA';
        }
    }

} 