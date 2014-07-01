<?php namespace eTrack\Courses;

use DB;
use eTrack\Core\Entity;
use eTrack\SubjectSectors\SubjectSector;

/**
 * Unit model
 *
 * @property string $id
 * @property integer $number
 * @property string $name
 * @property integer $credit_value
 * @property integer $glh
 * @property integer $level
 * @property float $subject_sector_id
 * @property-read SubjectSector $subject_sector
 * @property-read \Illuminate\Database\Eloquent\Collection|Criteria[] $criteria
 * @property-read \Illuminate\Database\Eloquent\Collection|Course[] $courses
 * @method static Unit listUnits($selectedSubjectSector = '', $searchString = '')
 * @author Jake Moreman <mail@jakemoreman.co.uk>
 */
class Unit extends Entity
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'unit';

    /**
     * The attributes that can be mass-assigned.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'number', 'name', 'credit_value', 'glh',
        'level', 'subject_sector_id',
        'number_of_pass_criteria', 'number_of_merit_criteria', 'number_of_distinction_criteria',
    ];


    /**
     * Validation rules for model.
     *
     * @var array
     */
    protected $validationRules = [
        'id'                             => 'required|max:12|unit_id|unique:unit,id,<id>',
        'number'                         => 'required|integer',
        'name'                           => 'required|max:100',
        'credit_value'                   => 'required|integer',
        'glh'                            => 'required|integer',
        'level'                          => 'required|integer|in:2,3',
        'subject_sector_id'              => 'required|exists:subject_sector,id',
        'number_of_pass_criteria'        => 'required|integer|min:1',
        'number_of_merit_criteria'       => 'required|integer|min:1',
        'number_of_distinction_criteria' => 'required|integer|min:1',
    ];

    /**
     * Alternative attribute names that are referenced in validation errors.
     *
     * @var array
     */
    protected $validationAttributeNames = [
        'id'     => 'unit code',
        'number' => 'unit number',
        'name'   => 'unit name',
        'glh'    => 'guided learning hours',
        'level'  => 'qualification level',
        'subject_sector_id' => 'subject sector',
    ];

    public static function boot()
    {
        parent::boot();

        static::saving(function (Unit $unit) {
            if ($unit->isValid()) {
                unset($unit->number_of_pass_criteria);
                unset($unit->number_of_merit_criteria);
                unset($unit->number_of_distinction_criteria);

                return true;
            }

            return true;
        });
    }

//    public function scopeListUnits($query, $selectedSubjectSector = '', $searchString = '')
//    {
//        return $query->select('unit.id as id', 'number', 'unit.name as name',
//            'credit_value', 'glh', 'level', 'subject_sector.name as subject_sector_name')
//            ->join('subject_sector', 'unit.subject_sector_id', '=', 'subject_sector.id')
//            ->orderBy('subject_sector.name')
//            ->orderBy('unit.number')
//            ->where('subject_sector_id', 'LIKE', $selectedSubjectSector)
//            ->where(function ($query) use ($searchString) {
//                $query->where('unit.id', 'LIKE', $searchString)
//                    ->orWhere('unit.number', 'LIKE', $searchString)
//                    ->orWhere('unit.name', 'LIKE', $searchString);
//            });
//    }

    public function subject_sector()
    {
        return $this->belongsTo('eTrack\SubjectSectors\SubjectSector');
    }

    public function courses()
    {
        return $this->belongsToMany('eTrack\Courses\Course');
    }

    public function criteria()
    {
        return $this->hasMany('eTrack\Courses\Criteria')
            ->orderBy(DB::raw('left(`id`, 1)'), 'desc')
            ->orderBy(DB::raw('left(`id`, 2)'));
    }

    public function passCriteria()
    {
        return $this->hasMany('eTrack\Courses\Criteria')->where('type', 'Pass');
    }

    public function meritCriteria()
    {
        return $this->hasMany('eTrack\Courses\Criteria')->where('type', 'Merit');
    }

    public function distinctionCriteria()
    {
        return $this->hasMany('eTrack\Courses\Criteria')->where('type', 'Distinction');
    }

    public function studentGrades()
    {
        return $this->hasMany('eTrack\Courses\StudentUnit');
    }

    public function delete()
    {
        return DB::transaction(function () {
            $this->criteria()->delete();
            parent::delete();
        });
    }
}