<?php namespace eTrack\Controllers\Admin;

use eTrack\Validation\Forms\Admin\Users\CreateValidator;
use eTrack\Validation\Forms\Admin\Users\EditValidator;
use eTrack\Validation\Forms\Admin\Users\Import\Step1Validator;
use eTrack\Validation\FormValidationException;
use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\LexerConfig;
use Goodby\CSV\Import\Standard\Lexer as Lexer;
use Illuminate\Database\QueryException;
use Request;
use Input;
use Hash;
use User;
use View;
use Session;
use Redirect;
use Str;
use PDF;
use DB;
use App;

class UserController extends \BaseController {

    /**
     * @var eTrack\Validation\Forms\Admin\Users\CreateValidator
     */
    protected $createFormValidator;

    /**
     * @var eTrack\Validation\Forms\Admin\Users\EditValidator
     */
    protected $editFormValidator;

    /**
     * @var eTrack\Validation\Forms\Admin\Users\Import\Step1Validator
     */
    protected $importStep1FormValidator;

    public function __construct(CreateValidator $createValidator, EditValidator $editValidator,
                                Step1Validator $step1Validator)
    {
        $this->createFormValidator = $createValidator;
        $this->editFormValidator = $editValidator;
        $this->importStep1FormValidator = $step1Validator;
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

        if (! $user) {
            return App::abort(404);
        }

        return View::make('admin.users.edit', array('user' => $user));
    }

    public function update($userId)
    {
        $formAttributes = array(
            'user_id'               => Input::get('id'),
            'full_name'             => Input::get('full_name'),
            'email_address'         => Input::get('email'),
            'password'              => Input::get('password'),
            'password_confirmation' => Input::get('password_confirmation'),
            'user_role'             => Input::get('role')
        );

        try {
            $this->editFormValidator->validate($formAttributes);
        } catch (FormValidationException $ex) {
            return Redirect::back()
                ->withInput(Input::except(array('password', 'password_confirmation')))
                ->withErrors($ex->getErrors());
        }

        $user = User::find($userId);

        $user->full_name = $formAttributes['full_name'];
        $user->email = $formAttributes['email_address'];
        $user->password = Hash::make($formAttributes['password']);
        $user->role = $formAttributes['user_role'];

        $user->save();

        return Redirect::route('admin.users.index')
            ->with('successMessage', 'Updated user account');
    }

    public function deleteConfirm($userId)
    {
        $user = User::find($userId);

        if (Request::ajax())
        {
            return View::make('admin.users.delete.modal', array('user' => $user));
        }

        return View::make('admin.users.delete.fallback', array('user' => $user));
    }

    public function destroy($userId)
    {
        try {
            $user = User::find($userId);
            $user->delete();
        } catch (QueryException $ex) {
            return Redirect::route('admin.users.index')
                ->with('errorMessage', 'Unable to delete user');
        }

        return Redirect::back()
            ->withInput()
            ->with('successMessage', 'Deleted user');

    }

    public function importStep1()
    {
        return View::make('admin.users.import.step1');
    }

    public function importStep1Store()
    {
        $formAttributes = array(
            'file' => Input::get('file'),
        );

        try {
            $this->importStep1FormValidator->validate($formAttributes);
        } catch (FormValidationException $ex) {
            return Redirect::back()
                ->withInput()
                ->withErrors($ex->getErrors());
        }

        if (Input::hasFile('file'))
        {
            $file = Input::file('file');
            $filePath = public_path().'/uploads/';
            $uploadSuccess = $file->move($filePath, 'user_import.csv');

            if ($uploadSuccess)
            {
                $importedData = array();

                $csvImportConfig = new LexerConfig();
                $csvImportLexer = new Lexer($csvImportConfig);
                $csvImportInterpreter = new Interpreter();

                $csvImportInterpreter->addObserver(function(array $row) use (&$importedData)
                {
                    $randomPassword = Str::random();

                    $importedData[] = array(
                        'id'        => $row[0],
                        'full_name' => $row[1],
                        'email'     => $row[2],
                        'role'      => $row[3],
                        'password'  => $randomPassword,
                    );
                });

                $csvImportLexer->parse($filePath.'user_import.csv', $csvImportInterpreter);

                // Delete uploaded file as we don't need it anymore
                unlink($filePath.'user_import.csv');

                Session::put('user_import_data', $importedData);

                // Free up memory by removing the imported data array
                unset($importedData);

                return Redirect::route('admin.users.import.step2');
            }
        }

        return Redirect::back()
            ->with('errorMessage', 'File didn\'t upload properly');
    }

    public function importStep2()
    {
        if (! Session::has('user_import_data'))
        {
            App::abort(404);
        }

        $users = Session::get('user_import_data');

        return View::make('admin.users.import.step2', array('users' => $users));
    }

    public function importStep2Store()
    {
        $users = Session::get('user_import_data');

        switch (Input::get('submit'))
        {
            case 'accept':
                if (DB::table('user')->insert($users))
                {
                    return Redirect::route('admin.users.import.step3');
                }

                return Redirect::route('admin.users.import.step3')
                    ->with('errorMessage', 'Unable to add user records to database.');
                ;;
            case 'cancel':
                Session::forget('user_import_data');
                return Redirect::route('admin.users.index')
                    ->with('infoMessage', 'Batch import cancelled');
                ;;
            default:
                App::abort(404);
        }
    }

    public function importStep3()
    {
        if (! Session::has('user_import_data'))
        {
            App::abort(404);
        }

        $users = Session::get('user_import_data');

        return View::make('admin.users.import.step3', array('users' => $users));
    }

    public function importStep3Store()
    {
        if (Input::get('submit') === 'complete')
        {
            Session::forget('user_import_data');
            return Redirect::route('admin.users.index');
        }

        return App::abort(404);
    }

    public function importPrint()
    {
        if (! Session::has('user_import_data'))
        {
            App::abort(404);
        }

        $users = Session::get('user_import_data');

        $pdf = PDF::loadView('admin.users.import.print', array('users' => $users));
        return $pdf->stream();
    }

}
