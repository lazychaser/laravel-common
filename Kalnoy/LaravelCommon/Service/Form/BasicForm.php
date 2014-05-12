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
        if ($this->valid($input))
        {
            return $this->run($input);
        }

        return false;
    }

}