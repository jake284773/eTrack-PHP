<?php namespace eTrack\Courses;

use DB;
use eTrack\Accounts\Student;
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
 * @property-read \Illuminate\Database\Eloquent\Collection|Enrolment[] $enrollments
 * @property-read \Illuminate\Database\Eloquent\Collection|Student[] $students
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

    protected $courseTypeClassMap = [
        'BTEC National Certificate' => 'eTrack\Courses\BTEC\National\NationalCertificate',
        'BTEC National Subsidiary Diploma' => 'eTrack\Courses\BTEC\National\NationalSubsidiaryDiploma',
        'BTEC National 90 Credit Diploma' => 'eTrack\Courses\BTEC\National\National90CreditDiploma',
        'BTEC National Diploma' => 'eTrack\Courses\BTEC\National\NationalDiploma',
        'BTEC National Extended Diploma' => 'eTrack\Courses\BTEC\National\NationalExtendedDiploma',
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

    /**
     * Overridden to check if the more specific course object can be instantiated.
     *
     * It is used when an existing course is found with a 'type' attribute.
     * This attribute determines what type of course it is. i.e. (Whether it's a
     * BTEC National Subsidiary or Extended Diploma).
     *
     * @param array $attributes
     * @return \Illuminate\Database\Eloquent\Model|static
     */
    public function newFromBuilder($attributes = [])
    {
        if ($attributes->type) {
            $class = $this->courseTypeClassMap[$attributes->type];
            $instance = new $class;
            $instance->exists = true;
            $instance->setRawAttributes((array) $attributes, true);
            return $instance;
        } else {
            return parent::newFromBuilder($attributes);
        }
    }

    public function units()
    {
        $units = $this->belongsToMany('eTrack\Courses\Unit', 'course_unit', 'course_id')
            ->orderBy('number')
            ->withPivot('unit_number');

//        foreach ($units as $unit) {
//            if ($unit->pivot->unit_number) {
//                $unit->number = $unit->pivot->unit_number;
//            }
//        }

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

    public function student_groups()
    {
        return $this->hasMany('eTrack\Courses\StudentGroup');
    }


    public function enrollments()
    {
        return $this->hasMany('eTrack\Courses\Enrolment');
    }

    public function students()
    {
        $pivotAttributes = [
            'final_grade', 'predicted_grade', 'target_grade',
            'final_ucas_tariff_score', 'predicted_ucas_tariff_score'
        ];

        return $this->belongsToMany('eTrack\Accounts\Student', 'course_student', 'course_id', 'student_user_id')
            ->withPivot($pivotAttributes)
            ->orderBy(DB::raw("substring_index(full_name, ' ', -1)"));
    }

}