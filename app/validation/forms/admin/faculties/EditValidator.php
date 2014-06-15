<?php namespace eTrack\Validation\Forms\Admin\Faculties;

use eTrack\Validation\FormValidator;

class EditValidator extends FormValidator {

    /**
     * Validation rules
     */
    protected $rules = array(
        'faculty_name' => 'required',
    );

}