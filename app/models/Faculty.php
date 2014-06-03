<?php

/**
 * Faculty model
 *
 * @property string $id
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection|Course[] $courses
 *
 */
class Faculty extends Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'faculty';

    /**
     * Disable timestamp management.
     *
     * @var bool
     */
    public $timestamps = false;

    public function courses()
    {
        return $this->hasMany('Course');
    }

}