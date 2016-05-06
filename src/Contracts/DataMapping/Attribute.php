<?php

namespace Kalnoy\LaravelCommon\Contracts\DataMapping;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface Attribute {

    /**
     * Do some stuff before items are imported.
     *
     * @param Collection $items
     *
     * @return $this
     */
    public function preload(Collection $items);

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function parse($value);

    /**
     * @param array $data
     * @param Model $model
     */
    public function setOn(array $data, Model $model);

    /**
     * @return string
     */
    public function getId();

}