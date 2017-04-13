<?php

namespace Kalnoy\LaravelCommon\DataMapping\Eloquent\Scheme;

class Enum extends BasicAttribute
{
    /**
     * @var array
     */
    private $options;

    /**
     * Enum constructor.
     *
     * @param $id
     * @param array $options
     * @param null $attribute
     */
    public function __construct($id, array $options, $attribute = null)
    {
        parent::__construct($id, $attribute);

        $this->options = $options;
    }

    /**
     * @param mixed $value
     *
     * @return mixed|null
     */
    public function parse($value)
    {
        return isset($this->options[$value]) ? $this->options[$value] : $value;
    }

    /**
     * @param $id
     * @param array $options
     * @param null $attribute
     *
     * @return Enum
     */
    public static function make($id, array $options, $attribute = null)
    {
        return new static($id, $options, $attribute);
    }
}