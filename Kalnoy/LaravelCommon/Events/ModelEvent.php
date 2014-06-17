<?php

namespace Kalnoy\LaravelCommon\Events;

class ModelEvent extends Event {

    /**
     * The model.
     * 
     * @var mixed
     */
    protected $model;

    /**
     * Init event.
     * 
     * @param mixed $model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Get a model.
     * 
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

}