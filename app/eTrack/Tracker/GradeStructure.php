<?php namespace eTrack\Tracker;

use eTrack\Core\AbstractReadOnlyStructure;

class GradeStructure extends AbstractReadOnlyStructure {
    protected $slug;
    protected $shortName;
    protected $longName;

    public function __construct($grade)
    {
        $this->slug = $this->produceSlug($grade);
        $this->shortName = $this->produceShortName($grade);
        $this->longName = $grade;
    }

    protected function produceShortName($grade)
    {
        $shortName = str_replace('Distinction', 'D', $grade);
        $shortName = str_replace('Merit', 'M', $shortName);
        $shortName = str_replace('Pass', 'P', $shortName);
        $shortName = str_replace('NYA', 'N', $shortName);

        return $shortName;
    }

    protected function produceSlug($grade)
    {
        $gradeSlug = $this->produceShortName($grade);

        // Distinction* grades contain an asterisk. Asterisks are not safe
        // characters as they are often reserved by the programming language.
        // So replace them with an 's'.
        $gradeSlug = str_replace('*', 's', $gradeSlug);
        // Also make the string all lowercase
        $gradeSlug = strtolower($gradeSlug);

        return $gradeSlug;
    }

} 