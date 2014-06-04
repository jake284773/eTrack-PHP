<?php namespace eTrack\Validation;

use Illuminate\Validation\Factory as Validator;
use Illuminate\Validation\Validator as ValidatorInstance;

abstract class FormValidator {

    /**
     * Validator factory
     *
     * @var Validator
     */
    protected $validator;

    /**
     * Validator object
     *
     * @var ValidatorInstance
     */
    protected $validation;

    /**
     * Validation rules
     *
     * @var array
     */
    protected $rules;

    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Validate the form data
     *
     * @param array $formData
     * @return mixed
     * @throws FormValidationException
     */
    public function validate(array $formData)
    {
        $this->validation = $this->validator->make($formData, $this->getValidationRules());

        if ($this->validation->fails())
        {
            throw new FormValidationException('Validation failed', $this->getValidationErrors());
        }

        return true;
    }

    /**
     * Get the validation rules
     *
     * @return array
     */
    protected function getValidationRules()
    {
        return $this->rules;
    }

    /**
     * Get the validation errors
     *
     * @return \Illuminate\Support\MessageBag
     */
    protected function getValidationErrors()
    {
        return $this->validation->errors();
    }

}