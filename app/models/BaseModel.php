<?php

class BaseModel extends Eloquent {

    /**
     * Disable timestamp management.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Disabled auto-incrementing of id field
     *
     * @var bool
     */
    public $incrementing = false;
}