<?php

namespace Kalnoy\LaravelCommon\Repo;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EloquentRepository
{
    /**
     * @var string
     */
    protected $modelClass;

    /**
     * @return Model
     */
    public function newModel()
    {
        return new $this->modelClass;
    }

    /**
     * @return Builder
     */
    public function newQuery()
    {
        return $this->newModel()->newQuery();
    }
}