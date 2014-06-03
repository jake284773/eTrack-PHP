<?php

/**
 * Faculty model
 *
 * @property string $id
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection|Course[] $courses
 *
 */
class Faculty extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'faculty';

    public function courses()
    {
        return $this->hasMany('Course');
    }

}