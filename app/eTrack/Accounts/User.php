<?php namespace eTrack\Accounts;

use eTrack\Core\Entity;
use Hash;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

/**
 * Class User
 *
 * @property string id
 * @property string password
 * @property string email
 * @property string full_name
 * @property string role
 *
 * @package eTrack\Accounts
 */
class User extends Entity implements UserInterface, RemindableInterface
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user';

    protected $validRoles = ['Admin', 'Course Organiser', 'Tutor', 'Student'];

    /**
     * The attributes that can be mass-assigned.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'email',
        'full_name',
        'password',
        'password_confirmation',
        'role'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Validation rules for model.
     *
     * @var array
     */
    protected $validationRules = [
        'id'                    => 'required|unique:user,id,<id>',
        'full_name'             => 'required',
        'email'                 => 'required|email',
        'password'              => 'required|confirmed',
        'role'                  => 'required',
    ];

    /**
     * Alternative attribute names that are referenced in validation errors.
     *
     * This is to avoid the issue of Laravel referring to a user ID as just "id".
     *
     * @var array
     */
    protected $validationAttributeNames = [
        'id'    => 'user ID',
        'email' => 'email address',
        'role'  => 'user role'
    ];

    public function __construct()
    {
        parent::__construct();

        // Make sure that the user role attribute is validated so only
        // supported roles can be used.
        $validRolesList = implode(",", $this->validRoles);
        $this->validationRules['role'] = 'required|in:'.$validRolesList;
    }

    /**
     * Add some handlers to certain events
     */
    public static function boot()
    {
        parent::boot();

        // Hash the password and remove the password_confirmation attribute
        // when saving the record to the database.
        static::saving(function (User $user) {
            if ($user->isValid()) {
                $user->password = Hash::make($user->password);
                unset($user->password_confirmation);

                return true;
            }

            return true;
        });
    }

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
     * @param  string $value
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

    /**
     * Override the Entity class method on getting prepared rules.
     *
     * This is to check whether the use is updating a record or not. If they
     * are then remove the "required" rule from the password field as changing
     * a user's password is an optional task.
     *
     * @return array
     */
    protected function getPreparedRules()
    {
        // Changing a user's password is optional when editing a user
        if ($this->exists())
        {
            $rules = parent::getPreparedRules();
            $rules['password'] = 'confirmed';
            return $rules;
        }

        return parent::getPreparedRules();
    }

    /**
     * Return an array of all the supported user roles.
     *
     * @return array
     */
    public function validRoles()
    {
        return $this->validRoles;
    }

}
