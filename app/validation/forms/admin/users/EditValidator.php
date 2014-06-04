<?php namespace eTrack\Validation\Forms\Admin\Users;

use eTrack\Validation\FormValidator;

class EditValidator extends FormValidator {

    /**
     * Validation rules
     */
    protected $rules = array(
        'full_name' => 'required',
        'email_address' => 'required|email',
        'password' => 'confirmed',
        'password_confirmation' => '',
        'user_role' => 'required|in:Admin,Course Organiser,Tutor,Student'
    );

}