<?php namespace eTrack\Controllers;

use Auth;
use DB;
use Hash;
use Input;
use Redirect;
use Validator;
use View;

class ProfileController extends BaseController
{

    public function index()
    {
        $user = Auth::user();

        return View::make('user.profile', ['user' => $user]);
    }

    public function store()
    {
        $validationRules = [
            'old_password' => 'required_with:password|current_password',
        ];

        $validator = Validator::make(Input::all(), $validationRules);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator->errors());
        }

        $user = Auth::user();
        $user->fill(Input::all());

        if (! $user->isValid()) {
            return Redirect::back()->withInput()->withErrors($user->getErrors());
        }

        try {
            DB::transaction(function() use($user) {
                $user->save();
            });
        } catch (\Exception $e) {
            return Redirect::back()->withInput()->with('errorMessage', 'Unable to update profile.');
        }

        return Redirect::back()->with('successMessage', 'Updated profile');
    }

} 