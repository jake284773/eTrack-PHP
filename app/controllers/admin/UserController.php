<?php namespace eTrack\Controllers\Admin;

use eTrack\Validation\Forms\Admin\Users\CreateValidator;
use eTrack\Validation\FormValidationException;
use Input;
use Hash;
use User;
use View;
use Redirect;

class UserController extends \BaseController {

    /**
     * @var eTrack\Validation\Forms\Admin\Users\CreateValidator
     */
    protected $createFormValidator;

    public function __construct(CreateValidator $createValidator)
    {
        $this->createFormValidator = $createValidator;
    }

    public function index()
    {
        $searchString = '%'.Input::get('search').'%';
        $selectedRole = '%'.Input::get('role').'%';

        $users = User::where('role', 'LIKE', $selectedRole)
            ->where(function($query) use($searchString)
            {
                $query->where('id', 'LIKE', $searchString)
                ->orWhere('full_name', 'LIKE', $searchString)
                ->orWhere('email', 'LIKE', $searchString);
            });

        $userCount = $users->count();

        if ($userCount > 1 or $userCount < 1) {
            $userCount = $userCount.' users';
        } else {
            $userCount = $userCount.' user';
        }

        return View::make('admin.users.index', array('users' => $users->paginate(15),
            'userCount' => $userCount));
    }

    public function create()
    {
        return View::make('admin.users.create');
    }

    public function store()
    {
        $formAttributes = array(
            'user_id'               => Input::get('userid'),
            'full_name'             => Input::get('fullname'),
            'email_address'         => Input::get('email'),
            'password'              => Input::get('password'),
            'password_confirmation' => Input::get('password_confirmation'),
            'user_role'             => Input::get('user-role')
        );

        try {
            $this->createFormValidator->validate($formAttributes);
        } catch (FormValidationException $ex) {
            return Redirect::back()
                ->withInput(Input::except(array('password', 'password_confirmation')))
                ->withErrors($ex->getErrors());
        }

        $user = new User();

        $user->id = $formAttributes['user_id'];
        $user->full_name = $formAttributes['full_name'];
        $user->email = $formAttributes['email_address'];
        $user->password = Hash::make($formAttributes['password']);
        $user->role = $formAttributes['user_role'];

        $user->save();

        return Redirect::route('admin.users.index')
            ->with('successMessage', 'Created new user');
    }

    public function edit($userId)
    {
        $user = User::find($userId);

        return View::make('admin.users.edit', array('user' => $user));
    }

}
