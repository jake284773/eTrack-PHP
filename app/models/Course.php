<?php

/**
 * Course model
 *
 * @property string $id
 * @property string $name
 * @property integer $level
 * @property string $status
 * @property string $pathway
 * @property-read \Illuminate\Database\Eloquent\Collection|Unit[] $units
 * @property-read \Illuminate\Database\Eloquent\Collection|Enrolment[] $enrollments
 * @property-read \Illuminate\Database\Eloquent\Collection|Student[] $students
 * @property-read User $courseOrganiser
 * @property-read SubjectSector $subjectSector
 * @property-read Faculty $faculty
 * @property string $type
 * @property float $subject_sector_id
 * @property string $faculty_id
 * @property string $course_organiser_user_id
 * @property-read \User $course_organiser
 */
class Course extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'course';

    public function units()
    {
        $units = $this->belongsToMany('Unit', 'course_unit')
            ->withPivot('unit_number');

        foreach ($units as $unit)
        {
            if ($unit->pivot->unit_number) {
                $unit->number = $unit->pivot->unit_number;
            }
        }

        return $units;
    }

    public function course_organiser()
    {
        return $this->belongsTo('User', 'course_organiser_user_id');
    }

    public function faculty()
    {
        return $this->belongsTo('Faculty');
    }

    public function subjectSector()
    {
        return $this->belongsTo('SubjectSector');
    }

    public function enrollments()
    {
        return $this->hasMany('Enrolment');
    }

    public function students()
    {
        $pivotAttributes = array('final_grade', 'predicted_grade', 'target_grade');

        if ($this->level === 3) {
            $pivotAttributes[] = 'final_ucas_tariff_score';
            $pivotAttributes[] = 'predicted_ucas_tariff_score';
        }

        return $this->belongsToMany('Student', 'course_student', 'course_id', 'student_user_id')
            ->withPivot($pivotAttributes);
    }

}