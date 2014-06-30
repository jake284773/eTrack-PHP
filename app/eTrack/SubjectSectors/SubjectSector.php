<?php namespace eTrack\SubjectSectors;
use eTrack\Core\Entity;
use eTrack\Courses\Course;
use eTrack\Courses\Unit;

/**
 * Subject sector model
 *
 * @property float $id
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection|Course[] $courses
 * @property-read \Illuminate\Database\Eloquent\Collection|Unit[] $units
 * @method static SubjectSector allWithUnits()
 */
class SubjectSector extends Entity
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'subject_sector';

    /**
     * The attributes that can be mass-assigned.
     *
     * @var array
     */
    protected $fillable = ['id', 'name'];

    /**
     * Validation rules for model.
     *
     * @var array
     */
    protected $validationRules = [
        'id'   => 'required|min:3|max:15',
        'name' => 'required|min:5|max:100',
    ];

    /**
     * Alternative attribute names that are referenced in validation errors.
     *
     * @var array
     */
    protected $validationAttributeNames = [
        'id'   => 'subject sector code',
        'name' => 'subject sector name',
    ];

    public function courses()
    {
        return $this->hasMany('eTrack\Courses\Course');
    }

    public function units()
    {
        return $this->hasMany('eTrack\Courses\Unit')
            ->orderBy('number', 'asc')
            ->orderBy('id', 'asc');
    }

//    public function scopeAllWithUnits($query)
//    {
//        return $query->join('unit', 'unit.subject_sector_id', '=', 'subject_sector.id')
//            ->select('subject_sector.id', 'subject_sector.name')
//            ->groupBy('subject_sector.name');
//    }

}