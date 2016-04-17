<?php

namespace Kalnoy\LaravelCommon\DataMapping\Eloquent\Events;

use Illuminate\Database\Eloquent\Model;

class ModelWasImported
{
    /**
     * @var Model
     */
    public $model;

    /**
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
}