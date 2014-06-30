<?php namespace eTrack\Core;

use Eloquent, Validator;
use eTrack\Core\Exceptions\NoValidationRulesFoundException;
use eTrack\Core\Exceptions\NoValidatorInstantiatedException;

abstract class Entity extends Eloquent
{
    /**
     * Disable timestamp management.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Disabled auto-incrementing of id field
     *
     * @var bool
     */
    public $incrementing = false;

    protected $validationRules = [];

    protected $updateValidationRules = [];

    protected $validationAttributeNames = [];

    protected $validator;

    public function isValid()
    {
        if (! isset($this->validationRules)) {
            throw new NoValidationRulesFoundException('No validation rule array defined in class ' . get_called_class());
        }

        $this->validator = Validator::make($this->getAttributes(), $this->getPreparedRules());

        if (isset($this->validationAttributeNames))
        {
            $this->validator->setAttributeNames($this->validationAttributeNames);
        }

        return $this->validator->passes();
    }

    public function getErrors()
    {
        if (! $this->validator) {
            throw new NoValidatorInstantiatedException;
        }

        return $this->validator->errors();
    }

    public function save(array $options = [])
    {
        if (! $this->isValid()) {
            return false;
        }

        return parent::save($options);
    }

    protected function getPreparedRules()
    {
        return $this->replaceIdsIfExists($this->validationRules);
    }

    protected function replaceIdsIfExists($rules)
    {
        $newRules = [];

        foreach ($rules as $key => $rule)
        {
            if (str_contains($rule, '<id>')) {
                $replacement = $this->exists ? $this->getAttribute($this->primaryKey) : '';

                $rule = str_replace('<id>', $replacement, $rule);
            }

            array_set($newRules, $key, $rule);
        }

        return $newRules;
    }
}