<?php

/**
 * Unit model
 *
 * @property string $code
 * @property string $name
 * @property integer $credit_value
 * @property integer $glh
 * @property integer $level
 * @property-read SubjectSector $subject_sector
 * @property-read \Illuminate\Database\Eloquent\Collection|Criteria[] $criteria
 *
 * @author Jake Moreman <mail@jakemoreman.co.uk>
 */
class Unit extends Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'unit';

    /**
     * Disable timestamp management.
     *
     * @var bool
     */
    public $timestamps = false;

    public function subject_sector()
    {
        return $this->belongsTo('SubjectSector');
    }

    public function criteria()
    {
        return $this->hasMany('Criteria');
    }

    public function getNumberAttribute()
    {
        if ($this->unit_number) {
            return $this->unit_number;
        }

        return ucfirst($this->number);
    }

} 