<?php namespace eTrack\Validation\Forms\Admin\Users;

use eTrack\Validation\FormValidator;

class CreateValidator extends FormValidator {

    /**
     * Validation rules
     */
    protected $rules = array(
        'user_id'  => 'required|unique:user,id',
        'full_name' => 'required',
        'email_address' => 'required|email',
        'password' => 'required|confirmed',
        'password_confirmation' => 'required',
        'user_role' => 'required|in:Admin,Course Organiser,Tutor,Student'
    );

}