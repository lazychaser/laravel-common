<?php

namespace Kalnoy\LaravelCommon\DataMapping\Eloquent\Scheme;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class BasicAttribute extends AbstractAttribute
{
    /**
     * @var mixed
     */
    public $default;

    /**
     * Do some stuff before items are imported.
     *
     * @param Collection $items
     *
     * @return $this
     */
    public function preload(Collection $items)
    {
        return $this;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function defaultValue($value)
    {
        $this->default = $value;

        return $this;
    }

    /**
     * @param array $data
     * @param Model $model
     *
     * @return $this
     */
    public function setOn(array $data, Model $model)
    {
        $model->setAttribute($this->attribute, $this->extractValue($data));
    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    protected function extractValue(array $data)
    {
        $value = $data[$this->id];

        return $value === '' || $value === null ? $this->default : $value;
    }

}