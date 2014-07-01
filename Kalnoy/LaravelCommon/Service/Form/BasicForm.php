<?php

namespace Kalnoy\LaravelCommon\Service\Form;

/**
 * Basic form class.
 */
abstract class BasicForm extends AbstractForm {

    /**
     * Run some action if input is valid.
     *
     * @param array $input
     *
     * @return mixed
     */
    abstract protected function run(array $input);

    /**
     * Process form.
     *
     * @param array $input
     *
     * @return bool
     */
    public function process(array $input)
    {
        $input = $this->processInput($input);
        
        if ($this->valid($input)) return $this->run($input);

        $this->whenInvalid();

        return false;
    }

    /**
     * Called when form validation has failed.
     */
    protected function whenInvalid() {}

    /**
     * Process input before validation.
     * 
     * @param array $input
     * 
     * @return array
     */
    protected function processInput(array $input)
    {
        return $input;
    }

}