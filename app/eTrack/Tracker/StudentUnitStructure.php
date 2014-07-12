<?php namespace eTrack\Tracker;

use eTrack\Core\AbstractReadOnlyStructure;
use eTrack\Courses\Unit;
use Illuminate\Database\Eloquent\Collection;

class StudentUnitStructure extends AbstractReadOnlyStructure {
    protected $student;
    protected $unit;
    protected $criteria;
    protected $grade;

    public function __construct(Unit $unit, GradeStructure $grade,
                                Collection $criteria = null,
                                StudentStructure $student = null)
    {
        $this->unit = 'Unit '.$unit->number.' - '.$unit->name;
        $this->criteria = $criteria;
        $this->grade = $grade;
        $this->student = $student;
    }
} 