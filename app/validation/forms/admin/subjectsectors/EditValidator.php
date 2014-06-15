<?php namespace eTrack\Validation\Forms\Admin\SubjectSectors;

use eTrack\Validation\FormValidator;

class EditValidator extends FormValidator {

    /**
     * Validation rules
     */
    protected $rules = array(
        'subject_sector_name' => 'required',
    );

}