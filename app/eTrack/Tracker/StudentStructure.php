<?php namespace eTrack\Tracker;

use eTrack\Accounts\Student;
use eTrack\Core\AbstractReadOnlyStructure;

class StudentStructure extends AbstractReadOnlyStructure {
    protected $shortName;
    protected $fullName;

    public function __construct(Student $student)
    {
        $this->shortName = $this->produceShortName($student);
        $this->fullName = $this->produceFullName($student);
    }

    protected function produceShortName(Student $student)
    {
        $firstInitial =  substr($student->full_name, 0, 1);
        $nameSplit = explode(' ', $student->full_name);
        $lastName = strtoupper(array_pop($nameSplit));

        return $lastName . ' ' . $firstInitial . '.';
    }

    protected function produceFullName(Student $student)
    {
        return $student->full_name.' ('.$student->id.')';
    }
} 