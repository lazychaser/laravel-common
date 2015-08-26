<?php

namespace Kalnoy\LaravelCommon\Html;

/**
 * Form builder.
 */
class FormBuilder extends \Collective\Html\FormBuilder {

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

    /**
     * Append some hidden fields. Multilevel arrays are supported.
     *
     * @param array $input
     * @param string $prefix
     *
     * @return string
     */
    public function appends(array $input, $prefix = '')
    {
        $html = '';

        foreach ($input as $key => $value)
        {
            if ($key === '_token') continue;

            if ($prefix) $key = $prefix.'['.$key.']';

            if (is_array($value))
            {
                $html .= $this->appends($value, $key);

                continue;
            }

            $html .= $this->hidden($key, $value).PHP_EOL;
        }

        return $html;
    }
}