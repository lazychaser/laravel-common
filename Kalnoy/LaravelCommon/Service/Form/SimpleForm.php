<?php

namespace Kalnoy\LaravelCommon\Service\Form;

use Kalnoy\LaravelCommon\Service\Validation\ValidableInterface;

trait SimpleForm {

    /**
     * @var \Kalnoy\LaravelCommon\Service\Validation\ValidableInterface
     */
    protected $validator;

    /**
     * Run the processing.
     *
     * @param array $input
     *
     * @return bool
     */
    abstract protected function run(array $input);

    /**
     * Get whether the form is valid.
     * 
     * @param array $input
     * 
     * @return bool
     */
    public function isValid(array $input)
    {
        return $this->valid($input, $this->validator);
    }

    /**
     * Process the form.
     * 
     * @param array $input
     * 
     * @return bool
     */
    public function process(array $input)
    {
        if ($this->isValid($input)) return $this->run($input);

        $this->whenInvalid();

        return false;
    }

    /**
     * Executed when the input is not valid.
     */
    protected function whenInvalid() {}

    /**
     * Set the validator.
     * 
     * @param \Kalnoy\LaravelCommon\Service\Validation\ValidableInterface $validator
     */
    public function setValidator(ValidableInterface $validator)
    {
        $this->validator = $validator;
    }
}