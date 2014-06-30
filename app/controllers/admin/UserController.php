<?php namespace eTrack\Controllers\Admin;

use eTrack\Accounts\UserRepository;
use eTrack\Controllers\BaseController;
use eTrack\Validation\FormValidationException;
use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\LexerConfig;
use Goodby\CSV\Import\Standard\Lexer as Lexer;
use Illuminate\Database\QueryException;
use Request;
use Input;
use Hash;
use eTrack\Accounts\User;
use View;
use Session;
use Redirect;
use Str;
use PDF;
use DB;
use App;

class UserController extends BaseController {

    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        if (($search = Input::get('search')) && ($role = Input::get('role'))) {
            $users = $this->userRepository->getPaginatedByRoleAndSearch($role, $search);
        } elseif ($search = Input::get('search')) {
            $users = $this->userRepository->getPaginatedBySearch($search);
        } elseif ($role = Input::get('role')) {
            $users = $this->userRepository->getPaginatedByRole($role);
        } else {
            $users = $this->userRepository->getAllPaginated();
        }

        $roles = $this->userRepository->getValidRoles();

        return View::make('admin.users.index', ['users' => $users,
            'roles' => $roles]);
    }

    public function create()
    {
        $roles = $this->userRepository->getValidRoles();
        return View::make('admin.users.create', ['roles' => $roles]);
    }

    public function store()
    {
        $user = $this->userRepository->getNew(Input::all());

        if (! $user->isValid())
        {
            return Redirect::back()->withInput()->withErrors($user->getErrors());
        }

        $user->save();

        return Redirect::route('admin.users.index')
            ->with('successMessage', 'Created new user');
    }

    public function edit($id)
    {
        $user = $this->userRepository->getById($id);

        if (! $user) App::abort(404);

        $roles = $this->userRepository->getValidRoles();

        return View::make('admin.users.edit', ['user' => $user, 'roles' => $roles]);
    }

    public function update($id)
    {
        $user = $this->userRepository->getById($id);
        $user->fill(Input::all());

        if (! $user->isValid())
        {
            return Redirect::back()->withInput()->withErrors($user->getErrors());
        }

        $user->save();

        return Redirect::route('admin.users.index')
            ->with('successMessage', 'Updated user account');
    }

    public function deleteConfirm($id)
    {
        $user = $this->userRepository->getById($id);

        if (Request::ajax())
        {
            return View::make('admin.users.delete.modal', ['user' => $user]);
        }

        return View::make('admin.users.delete.fallback', ['user' => $user]);
    }

    public function destroy($id)
    {
        try {
            $user = $this->userRepository->requireById($id);
            $user->delete();
        } catch (\Exception $ex) {
            return Redirect::route('admin.users.index')->with('errorMessage', 'Unable to delete user');
        }

        return Redirect::back()->withInput()->with('successMessage', 'Deleted user');

    }

    public function importStep1()
    {
        return View::make('admin.users.import.step1');
    }

    public function importStep1Store()
    {
         if (Input::hasFile('file'))
        {
            $file = Input::file('file');
            $filePath = public_path().'/uploads/';
            $uploadSuccess = $file->move($filePath, 'user_import.csv');

            if ($uploadSuccess)
            {
                $importedData = [];

                $csvImportConfig = new LexerConfig();
                $csvImportLexer = new Lexer($csvImportConfig);
                $csvImportInterpreter = new Interpreter();

                $csvImportInterpreter->addObserver(function(array $row) use (&$importedData)
                {
                    $randomPassword = Str::random();

                    $importedData[] = [
                        'id'        => $row[0],
                        'full_name' => $row[1],
                        'email'     => $row[2],
                        'role'      => $row[3],
                        'password'  => $randomPassword,
                    ];
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
            ->with('errorMessage', "File didn't upload properly");
    }

    public function importStep2()
    {
        if (! Session::has('user_import_data'))
        {
            App::abort(404);
        }

        $users = Session::get('user_import_data');

        return View::make('admin.users.import.step2', ['users' => $users]);
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
            case 'cancel':
                Session::forget('user_import_data');
                return Redirect::route('admin.users.index')
                    ->with('infoMessage', 'Batch import cancelled');
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

        return View::make('admin.users.import.step3', ['users' => $users]);
    }

    public function importStep3Store()
    {
        if (Input::get('submit') === 'complete')
        {
            Session::forget('user_import_data');
            return Redirect::route('admin.users.index');
        }

        App::abort(404);
    }

    public function importPrint()
    {
        if (! Session::has('user_import_data'))
        {
            App::abort(404);
        }

        $users = Session::get('user_import_data');

        $pdf = PDF::loadView('admin.users.import.print', ['users' => $users]);
        return $pdf->stream();
    }

}
