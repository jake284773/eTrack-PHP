<?php namespace eTrack\Courses;

use eTrack\Accounts\User;
use eTrack\Core\Entity;
use eTrack\Faculties\Faculty;
use eTrack\SubjectSectors\SubjectSector;

/**
 * Course model
 *
 * @property string $id
 * @property string $name
 * @property integer $level
 * @property string $status
 * @property string $pathway
 * @property-read \Illuminate\Database\Eloquent\Collection|Unit[] $units
// * @property-read \Illuminate\Database\Eloquent\Collection|Enrolment[] $enrollments
// * @property-read \Illuminate\Database\Eloquent\Collection|Student[] $students
 * @property-read User $courseOrganiser
 * @property-read SubjectSector $subjectSector
 * @property-read Faculty $faculty
 * @property string $type
 * @property float $subject_sector_id
 * @property string $faculty_id
 * @property string $course_organiser_user_id
 * @property-read User $course_organiser
 */
class Course extends Entity
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'course';

    /**
     * The supported course types
     *
     * @var array
     */
    protected $validTypes = [
        'BTEC National Certificate',
        'BTEC National Subsidiary Diploma',
        'BTEC National Extended Diploma',
    ];

    /**
     * The attributes that can be mass-assigned.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'level', 'type', 'pathway',
        'subject_sector', 'faculty', 'course_organiser'
    ];

    /**
     * Validation rules for model.
     *
     * @var array
     */
    protected $validationRules = [
        'id'   => 'required|min:3|max:15',
        'name' => 'required|min:5|max:100',
        'level' => 'required|in:2,3',
        'type' => 'required|max:100',
        'pathway' => 'max:100',
        'subject_sector' => 'required|exists:subject_sector,id,<id>',
        'faculty' => 'required|exists:faculty,id,<id>',
        'course_organiser' => 'required|exists:user,id,<id>,role,Course Organiser',
    ];

    /**
     * Alternative attribute names that are referenced in validation errors.
     *
     * @var array
     */
    protected $validationAttributeNames = [
        'id'    => 'course code',
        'name' => 'course name',
        'level' => 'qualification level',
        'type' => 'course type',
        'pathway' => 'course pathway',
    ];

    public function __construct()
    {
        parent::__construct();

        // Make sure that only supported course types can be entered
        $validTypesList = implode(",", $this->validTypes);
        $this->validationRules['type'] = $this->validationRules['type']."|in:".$validTypesList;
    }

    public function units()
    {
        $units = $this->belongsToMany('eTrack\Courses\Unit', 'course_unit')
            ->withPivot('unit_number');

        foreach ($units as $unit) {
            if ($unit->pivot->unit_number) {
                $unit->number = $unit->pivot->unit_number;
            }
        }

        return $units;
    }

    public function course_organiser()
    {
        return $this->belongsTo('eTrack\Accounts\User', 'course_organiser_user_id');
    }

    public function faculty()
    {
        return $this->belongsTo('eTrack\Faculties\Faculty');
    }

    public function subject_sector()
    {
        return $this->belongsTo('eTrack\SubjectSectors\SubjectSector');
    }
//
//    public function enrollments()
//    {
//        return $this->hasMany('eTrack\Models\Entities\Enrolment');
//    }
//
//    public function students()
//    {
//        $pivotAttributes = ['final_grade', 'predicted_grade', 'target_grade'];
//
//        if ($this->level === 3) {
//            $pivotAttributes[] = 'final_ucas_tariff_score';
//            $pivotAttributes[] = 'predicted_ucas_tariff_score';
//        }
//
//        return $this->belongsToMany('eTrack\Accounts\Student', 'course_student', 'course_id', 'student_user_id')
//            ->withPivot($pivotAttributes);
//    }

}