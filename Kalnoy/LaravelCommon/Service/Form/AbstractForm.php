<?php

namespace Kalnoy\LaravelCommon\Service\Form;

use Kalnoy\LaravelCommon\Service\Validation\ValidableInterface;

abstract class AbstractForm {

    /**
     * The validator.
     *
     * @var \App\Service\Validation\ValidableInterface
     */
    protected $validator;

    public function __construct(ValidableInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Validate and input using validator.
     *
     * @param  array  $input
     *
     * @return bool
     */
    public function valid(array $input)
    {
        return $this->validator->with($input)->passes();
    }

    /**
     * Get validation errors.
     *
     * @return array
     */
    function errors()
    {
        return $this->validator->errors();
    }
}