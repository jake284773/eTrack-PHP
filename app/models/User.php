<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

/**
 * User model
 *
 * @property string $user_id
 * @property string $password
 * @property string $email
 * @property string $remember_token
 * @property string $full_name
 * @property string $role
 *
 */
class User extends Eloquent implements UserInterface, RemindableInterface {

    /**
     * The supported roles that a user can have.
     *
     * @var array
     */
    protected $roles = array(
        'Admin',
        'Course Organiser',
        'Tutor',
        'Student'
    );

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'user';

    /**
     * Disable timestamp management.
     *
     * @var bool
     */
    public $timestamps = false;

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the token value for the "remember me" session.
	 *
	 * @return string
	 */
	public function getRememberToken()
	{
		return $this->remember_token;
	}

	/**
	 * Set the token value for the "remember me" session.
	 *
	 * @param  string  $value
	 * @return void
	 */
	public function setRememberToken($value)
	{
		$this->remember_token = $value;
	}

	/**
	 * Get the column name for the "remember me" token.
	 *
	 * @return string
	 */
	public function getRememberTokenName()
	{
		return 'remember_token';
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}

}
