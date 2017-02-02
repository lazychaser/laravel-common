<?php

namespace Kalnoy\LaravelCommon\Html;

/**
 * Form builder.
 */
class FormBuilder extends \Collective\Html\FormBuilder
{

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
    public function label($name, $value = null, $options = [ ], $escape_html = true)
    {
        return parent::label($this->getIdFromName($name), $value, $options, $escape_html);
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

        foreach ($input as $key => $value) {
            if ($key === '_token') continue;

            if ($prefix) $key = $prefix.'['.$key.']';

            if (is_array($value)) {
                $html .= $this->appends($value, $key);

                continue;
            }

            $html .= $this->hidden($key, $value).PHP_EOL;
        }

        return $html;
    }

    /**
     * @param string $type
     * @param string $name
     * @param string $label
     * @param string $value
     * @param bool $active
     * @param array $attributes
     *
     * @return string
     */
    public function checkableButton($type, $name, $label, $value,
                                    $active = null, array $attributes = [ ]
    ) {
        $active = $this->getCheckedState($type, $name, $value, $active);

        if ($active) $this->html->appendClass($attributes, 'active');

        $attributes['name'] = $name;
        $attributes['value'] = $value;

        if ( ! array_key_exists('type', $attributes)) {
            $attributes['type'] = 'submit';
        }

        return $this->button($label, $attributes);
    }

    /**
     * @param string $name
     * @param string $label
     * @param string $value
     * @param bool $active
     * @param array $attributes
     *
     * @return string
     */
    public function radioButton($name, $label, $value, $active = null,
                                array $attributes = [ ]
    ) {
        return $this->checkableButton('radio', $name, $label, $value, $active, $attributes);
    }

    /**
     * @param string $name
     * @param string $label
     * @param string $value
     * @param bool $active
     * @param array $attributes
     *
     * @return string
     */
    public function checkboxButton($name, $label, $value, $active = null,
                                   array $attributes = [ ]
    ) {
        return $this->checkableButton('checkbox', $name, $label, $value, $active, $attributes);
    }

}