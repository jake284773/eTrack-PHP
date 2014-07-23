<?php namespace eTrack\Courses;

use DB;
use eTrack\Accounts\Student;
use eTrack\Accounts\User;
use eTrack\Core\Entity;
use eTrack\Faculties\Faculty;
use eTrack\GradeCalculators\CourseGrade;
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
     * The qualification framework that the course is for.
     * @var string
     */
    protected $qualificationFramework;

    /**
     * The supported course types
     *
     * @var array
     * @static
     */
    public static $validTypes = [
        'BTEC National Certificate',
        'BTEC National Subsidiary Diploma',
        'BTEC National 90 Credit Diploma',
        'BTEC National Diploma',
        'BTEC National Extended Diploma',

        'Cambridge Technical Certificate',
        'Cambridge Technical Introductory Diploma',
        'Cambridge Technical Subsidiary Diploma',
        'Cambridge Technical Diploma',
        'Cambridge Technical Extended Diploma',
    ];

    /**
     * A key value array that maps the course types to the specialised course
     * classes.
     *
     * @var array
     * @static
     */
    public static $courseTypeClassMap = [
        'BTEC National Certificate' => 'eTrack\Courses\BTEC\National\NationalCertificate',
        'BTEC National Subsidiary Diploma' => 'eTrack\Courses\BTEC\National\NationalSubsidiaryDiploma',
        'BTEC National 90 Credit Diploma' => 'eTrack\Courses\BTEC\National\National90CreditDiploma',
        'BTEC National Diploma' => 'eTrack\Courses\BTEC\National\NationalDiploma',
        'BTEC National Extended Diploma' => 'eTrack\Courses\BTEC\National\NationalExtendedDiploma',

        'Cambridge Technical Certificate' => 'eTrack\Courses\CambridgeTechnical\CTCertificate',
        'Cambridge Technical Introductory Diploma' => 'eTrack\Courses\CambridgeTechnical\CTIntroductoryDiploma',
        'Cambridge Technical Subsidiary Diploma' => 'eTrack\Courses\CambridgeTechnical\CTSubsidiaryDiploma',
        'Cambridge Technical Diploma' => 'eTrack\Courses\CambridgeTechnical\CTDiploma',
        'Cambridge Technical Extended Diploma' => 'eTrack\Courses\CambridgeTechnical\CTExtendedDiploma',
    ];

    /**
     * An array of all the possible grades in the form of grade objects.
     * @var CourseGrade[]
     */
    protected $possibleGrades = [];

    /**
     * The total number of credit points this course provides.
     * @var integer
     */
    protected $totalCredits;

    /**
     * The attributes that can be mass-assigned.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'level',
        'type',
        'pathway',
        'subject_sector_id',
        'faculty_id',
        'course_organiser_user_id'
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
        'subject_sector_id' => 'required|exists:subject_sector,id',
        'faculty_id' => 'required|exists:faculty,id',
        'course_organiser_user_id' => 'required|exists:user,id,role,Course Organiser',
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
        'subject_sector_id' => 'subject sector',
        'faculty_id' => 'faculty',
        'course_organiser_user_id' => 'course organiser',
    ];

    public function __construct($attributes = array())
    {
        parent::__construct($attributes);

        // Make sure that only supported course types can be entered
        $validTypesList = implode(",", Course::$validTypes);
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
            $class = Course::$courseTypeClassMap[$attributes->type];
            $instance = new $class;
            $instance->exists = true;
            $instance->setRawAttributes((array) $attributes, true);
            return $instance;
        } else {
            return parent::newFromBuilder($attributes);
        }
    }

    /**
     * Retrieves the Course Grade object based on the grade string parameter.
     *
     * @param string $grade The grade to search for in the shorthand format
     * (i.e. DDM).
     * @throws \InvalidArgumentException
     * @return CourseGrade
     */
    public function getGrade($grade)
    {
        foreach ($this->possibleGrades as $grading)
        {
            if ($grading->getGrade() === $grade)
            {
                return $grading;
            }
        }

        throw new \InvalidArgumentException('Specified grade could not be found.');
    }

    /**
     * Gets the possible grades array for the course.
     * @return CourseGrade[]
     */
    public function getPossibleGrades()
    {
        return $this->possibleGrades;
    }

    public function units()
    {
        return $this->belongsToMany('eTrack\Courses\Unit', 'course_unit', 'course_id')
            ->withPivot('unit_number')
            ->orderBy('pivot_unit_number');
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
        return $this->hasMany('eTrack\Courses\StudentGroup', 'course_id');
    }

    /**
     * Retrieve all the students that are enrolled on the course including the
     * specific enrollment details (i.e. final grades)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
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

    /**
     * Retrieves all the students enrolled on the course which are not part of
     * any student group.
     *
     * This is used in the student group creation form in the student selection
     * box, to determine which students can be added to a new group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function studentsNotInGroup()
    {
        // Retrieve an array list of all the student IDs that already belong to a group.
        $studentIdsInGroup = DB::table('student_group')
            ->join('student_group_student', 'student_group.id', '=', 'student_group_student.student_group_id')
            ->where('student_group.course_id', '=', $this->id)
            ->lists('student_user_id');

        $relationship = $this->belongsToMany('eTrack\Accounts\Student', 'course_student', 'course_id', 'student_user_id')
            ->orderBy(DB::raw("substring_index(full_name, ' ', -1)"));

        // If there are any enrolled students in any of the groups for this
        // course, then exclude them from the result.
        if ($studentIdsInGroup) {
            $relationship = $relationship->whereNotIn('id', $studentIdsInGroup);
        }

        return $relationship;
    }

    public function studentsNotOnCourse()
    {
        // Retrieve an array list of all the student IDs that are already enrolled
        // on the course.
        $studentIdsAlreadyEnrolled = DB::table('course_student')
            ->where('course_student.course_id', '=', $this->id)
            ->lists('student_user_id');

        $relationship = $this->belongsToMany('eTrack\Accounts\Student', 'course_student', 'course_id', 'student_user_id')
            ->orderBy(DB::raw("substring_index(full_name, ' ', -1)"));

        // If there are any enrolled students in any of the groups for this
        // course, then exclude them from the result.
        if ($studentIdsAlreadyEnrolled) {
            $relationship = $relationship->whereNotIn('id', $studentIdsAlreadyEnrolled);
        }

        return $relationship;
    }

    public function getValidTypesAttribute()
    {
        return $this->validTypes;
    }

}