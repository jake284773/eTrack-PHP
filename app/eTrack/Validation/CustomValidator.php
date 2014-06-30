<?php namespace eTrack\Validation;

use eTrack\Accounts\User;
use Illuminate\Validation\Validator;
use Auth;
use Hash;

/**
 * Class for storing custom validator methods.
 *
 * @package eTrack\Validators
 * @author Jake Moreman <mail@jakemoreman.co.uk>
 */
class CustomValidator extends Validator {

    /**
     * Validates successfully if the field only contains letters of the alphabet and spaces.
     *
     * @param $attribute
     * @param string $value
     * @param $params
     * @return bool
     */
    public function validateAlphaSpaces($attribute, $value, $params)
    {
        return preg_match('/^[\pL\s]+$/u', $value);
    }

    /**
     * Validates successfully if the field matches the current password for the user.
     *
     * @param $attribute
     * @param string $value
     * @param $params
     * @return bool
     */
    public function validateCurrentPassword($attribute, $value, $params)
    {
		$user = new User();

        $userObject = $user->find(Auth::user()->id);
        $currentPassword = $userObject->password;

		if (Hash::check($value, $currentPassword))
			return true;

		return false;
    }

    /**
     * Validates successfully if the field matches the correct format for the BTEC unit code.
     *
     * @param $attribute
     * @param string $value
     * @param $params
     * @return bool
     */
    public function validateUnitId($attribute, $value, $params)
    {
    	return preg_match("/[A-Z](-)\d{3}(-)\d{4}$/", $value);
    }
}