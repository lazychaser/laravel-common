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
     * @param string $key
     *
     * @return string
     */
    public function appends(array $input, $key = '')
    {
        $html = '';

        foreach ($input as $inner => $value)
        {
            if ($key === '_token') continue;

            if ($key) $inner = $key.'['.$inner.']';

            if (is_array($value))
            {
                $html .= $this->appends($value, $inner);

                continue;
            }

            $html .= '<input type="hidden" name="'.$inner.'" value="'.$this->html->entities($value).'">'.PHP_EOL;
        }

        return $html;
    }
}