<?php

namespace Kalnoy\LaravelCommon\Traits;

trait ActiveSwitch {

    /**
     * Attribute mutator.
     *
     * @param int $value
     *
     * @return bool
     */
    public function getIsActiveAttribute($value)
    {
        return (bool)$value;
    }

    /**
     * Active a model.
     *
     * @return $this
     */
    public function activate()
    {
        $this->attributes['is_active'] = 1;

        return $this;
    }

    /**
     * Deactivate a model.
     *
     * @return $this
     */
    public function deactivate()
    {
        $this->attributes['is_active'] = 0;

        return $this;
    }
}