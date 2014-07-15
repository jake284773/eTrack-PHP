<?php namespace eTrack\SubjectSectors;

use eTrack\Core\Entity;
use eTrack\Courses\Course;
use eTrack\Courses\Unit;

/**
 * Subject sector eloquent model
 *
 * @property float $id
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection|Course[] $courses
 * @property-read \Illuminate\Database\Eloquent\Collection|Unit[] $units
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

    /**
     * One-to-many relationship with subject sectors and courses.
     *
     * (One subject sector has many courses)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courses()
    {
        return $this->hasMany('eTrack\Courses\Course');
    }

    /**
     * One-to-many relationship with subject sectors and units.
     *
     * (One subject sector has many units)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function units()
    {
        return $this->hasMany('eTrack\Courses\Unit')
            ->orderBy('number', 'asc')
            ->orderBy('id', 'asc');
    }

    /**
     * Query scope for ordering records by the name field in ascending order.
     *
     * @param $query
     * @return mixed
     */
    public function scopeNameAscending($query)
    {
        return $query->orderBy('name');
    }
}