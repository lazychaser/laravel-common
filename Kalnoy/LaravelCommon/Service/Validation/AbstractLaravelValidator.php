<?php

namespace Kalnoy\LaravelCommon\Service\Validation;

use Illuminate\Validation\Factory;

abstract class AbstractLaravelValidator implements ValidableInterface {

    /**
     * Validator
     *
     * @var \Illuminate\Validation\Factory
     */
    protected $validator;

    /**
     * Validation data key => value array
     *
     * @var Array
     */
    protected $data = array();

    /**
     * Validation errors
     *
     * @var Array
     */
    protected $errors = array();

    /**
     * Validation rules
     *
     * @var Array
     */
    protected $rules = array();

    /**
     * Validation messages.
     *
     * @var array
     */
    protected $messages = array();

    public function __construct(Factory $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Set data to validate
     *
     * @return \App\Service\Validation\AbstractLaravelValidator
     */
    public function with(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return $this->rules;
    }

    /**
     * Validation passes or fails.
     *
     * @return bool
     */
    public function passes()
    {
        $validator = $this->validator->make($this->data, $this->rules(), $this->messages);

        $this->setup($validator);

        if ($validator->fails())
        {
            $this->errors = $validator->messages()->getMessages();

            return false;
        }

        return true;
    }

    /**
     * Configure validator.
     *
     * @param \Illuminate\Validation\Validator $v
     *
     * @return void
     */
    protected function setup($v)
    {
        
    }

    /**
     * Return errors, if any.
     *
     * @return array
     */
    public function errors()
    {
        return $this->errors;
    }
}