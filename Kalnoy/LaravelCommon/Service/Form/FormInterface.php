<?php

namespace Kalnoy\LaravelCommon\Service\Form;

/**
 * The interface that is required for FormController.
 */
interface FormInterface {

    /**
     * Process the input.
     *
     * @param array $input
     *
     * @return bool
     */
    public function process(array $input);

    /**
     * Get a message that indicates the result.
     *
     * @return \Kalnoy\LaravelCommon\Service\Form\Alert
     */
    public function message();

    /**
     * Get error messages.
     *
     * @return array
     */
    public function errors();

    /**
     * Get the name of the field of the form.
     * 
     * @param string $name
     * 
     * @return string
     */
    public function field($name);

    /**
     * Get the id of the form.
     *
     * @return string
     */
    public function id();

}