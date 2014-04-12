<?php

namespace Kalnoy\LaravelCommon\Service\Validation;

interface ValidableInterface {

    /**
     * Set the data to validate.
     *
     * @param  array  $input
     *
     * @return ValidableInterface
     */
    public function with(array $input);

    /**
     * Get whether data passes validation.
     *
     * @return bool
     */
    public function passes();

    /**
     * Get validation errors.
     *
     * @return array
     */
    public function errors();
}