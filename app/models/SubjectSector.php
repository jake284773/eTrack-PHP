<?php

/**
 * Subject sector model
 *
 * @property float $id
 * @property string $name
 */
class SubjectSector extends Eloquent {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'subject_sector';

    /**
     * Disable timestamp management.
     *
     * @var bool
     */
    public $timestamps = false;

} 