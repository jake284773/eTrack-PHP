<?php namespace eTrack\Validation\Forms\Admin\Faculties;

use eTrack\Validation\FormValidator;

class CreateValidator extends FormValidator {

    /**
     * Validation rules
     */
    protected $rules = array(
        'faculty_code' => 'required|unique:faculty,id',
        'faculty_name' => 'required',
    );

}