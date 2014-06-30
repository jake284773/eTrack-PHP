<?php namespace eTrack\Faculties;
use eTrack\Core\Entity;

use eTrack\Courses\Course;

/**
 * Faculty model
 *
 * @property string $id
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection|Course[] $courses
 */
class Faculty extends Entity
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'faculty';

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
     * This is to avoid the issue of Laravel referring to a faculty code as just "id".
     *
     * @var array
     */
    protected $validationAttributeNames = [
        'id'   => 'faculty code',
        'name' => 'faculty name',
    ];

    /**
     * Relation method for retrieving all the courses that are part of this
     * faculty.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courses()
    {
        return $this->hasMany('eTrack\Courses\Course');
    }

}