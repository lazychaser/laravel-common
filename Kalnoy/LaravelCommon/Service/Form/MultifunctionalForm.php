<?php

namespace Kalnoy\LaravelCommon\Service\Form;

trait MultifunctionalForm {

    /**
     * Get the default action.
     *
     * @return string
     */
    abstract protected function getDefaultAction();

    /**
     * {@inheritdoc}
     */
    public function process(array $input)
    {
        if ( ! $action = array_pull($input, 'action', $this->getDefaultAction()))
        {
            return false;
        }

        $action = 'do'.ucfirst($action);

        return method_exists($this, $action) ? $this->{$action}($input) !== false : false;
    }

}