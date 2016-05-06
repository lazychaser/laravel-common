<?php

namespace Kalnoy\LaravelCommon\DataMapping\Eloquent\Scheme;

use Kalnoy\LaravelCommon\Service\Helpers;

class ScalarAttribute extends BasicAttribute
{
    /**
     * @var string
     */
    protected $type = 'string';

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function parse($value)
    {
        if ($value === null) return $value;

        switch ($this->type) {
            case 'bool':
                return (bool)$value;

            case 'int':
                return (int)Helpers::parseNumber($value);

            case 'float':
                return (float)Helpers::parseNumber($value);

            case 'number':
                return Helpers::parseNumber($value);
        }

        return is_string($value) ? trim($value) : $value;
    }

    /**
     * @return $this
     */
    public function bool()
    {
        return $this->setType('bool');
    }

    /**
     * @return $this
     */
    public function int()
    {
        return $this->setType('int');
    }

    /**
     * @return $this
     */
    public function float()
    {
        return $this->setType('float');
    }

    /**
     * @return $this
     */
    public function number()
    {
        return $this->setType('number');
    }

    /**
     * @return $this
     */
    public function string()
    {
        return $this->setType('string');
    }

    /**
     * @param $value
     *
     * @return $this
     */
    protected function setType($value)
    {
        $this->type = $value;

        return $this;
    }
}