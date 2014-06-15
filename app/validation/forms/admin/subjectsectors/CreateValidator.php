<?php namespace eTrack\Validation\Forms\Admin\SubjectSectors;

use eTrack\Validation\FormValidator;

class CreateValidator extends FormValidator {

    /**
     * Validation rules
     */
    protected $rules = array(
        'subject_sector_id' => 'required|unique:subject_sector,id',
        'subject_sector_name' => 'required',
    );

}