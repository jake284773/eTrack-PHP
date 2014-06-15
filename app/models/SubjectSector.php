<?php

/**
 * Subject sector model
 *
 * @property float $id
 * @property string $name
 */
class SubjectSector extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'subject_sector';

    public function courses()
    {
        return $this->hasMany('Course');
    }

    public function units()
    {
        return $this->hasMany('Unit')
          ->orderBy('number', 'asc')
          ->orderBy('id', 'asc');
    }

}