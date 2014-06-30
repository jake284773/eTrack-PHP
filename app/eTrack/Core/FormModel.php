<?php namespace eTrack\Core;

use Validator, App;
use eTrack\Core\Exceptions\NoValidationRulesFoundException;

class FormModel {

    protected $inputData;

    protected $validationRules;

    protected $validator;

    public function __construct()
    {
        $this->inputData = App::make('request')->all();
    }

    public function getInputData()
    {
        return $this->inputData;
    }

    public function isValid()
    {
        $this->beforeValidation();

        if (! isset($this->validationRules)) {
            throw new NoValidationRulesFoundException('No validation rules found in class ' . get_called_class());
        }

        $this->validator = Validator::make($this->inputData, $this->validationRules);

        return $this->validator->passes();
    }

    public function getErrors()
    {
        return $this->validator->errors();
    }

    protected function beforeValidation() {}

}