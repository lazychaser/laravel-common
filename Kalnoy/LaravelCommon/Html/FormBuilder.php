<?php

namespace Kalnoy\LaravelCommon\Html;

/**
 * Form builder.
 */
class FormBuilder extends \Illuminate\Html\FormBuilder {

    /**
     * Generate an id from name.
     *
     * @param string $name
     *
     * @return string
     */
    public function getIdFromName($name)
    {
        return strtr($name, [ '[]' => '', '[' => '__', ']' => '' ]);
    }

    /**
     * @inheritdoc
     *
     * @param string $name
     * @param string $value
     * @param array $options
     *
     * @return string
     */
    public function label($name, $value = null, $options = [])            
    {
        return parent::label($this->getIdFromName($name), $value, $options);
    }

    /**
     * @inheritdoc
     *
     * @param string $name
     * @param array $attributes
     *
     * @return string
     */
    public function getIdAttribute($name, $attributes)
    {
        return parent::getIdAttribute($this->getIdFromName($name), $attributes);
    }
}