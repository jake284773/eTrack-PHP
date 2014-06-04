<?php namespace eTrack\Validation\Forms\Admin\Users\Import;

use eTrack\Validation\FormValidator;

class Step1Validator extends FormValidator {

    /**
     * Validation rules
     */
    protected $rules = array(
        'file' => 'mimes:csv'
    );

}