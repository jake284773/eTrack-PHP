<?php namespace eTrack\Tracker;

use eTrack\Core\AbstractReadOnlyStructure;
use Illuminate\Database\Eloquent\Collection;

class CourseStudentStructure extends AbstractReadOnlyStructure {
    public $student;
    protected $units;

    protected $finalGrade;
    protected $predictedGrade;
    protected $targetGrade;

    protected $finalUcasPoints;
    protected $predictedUcasPoints;

   public function __construct(StudentStructure $student, Collection $units,
                               GradeStructure $finalGrade, GradeStructure $predictedGrade,
                               GradeStructure $targetGrade, $finalUcasPoints = null,
                               $predictedUcasPoints = null)
   {
       $this->student = $student;

       $this->units = $units;

       $this->finalGrade = $finalGrade;
       $this->predictedGrade = $predictedGrade;
       $this->targetGrade = $targetGrade;

       $this->finalUcasPoints = $finalUcasPoints;
       $this->predictedUcasPoints = $predictedUcasPoints;
   }
} 