<?php

namespace Kalnoy\LaravelCommon\Repo;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\LaravelCommon\DataMapping\DataSources\Xml\Collection;

/**
 * Class EloquentRepository
 *
 * @package Kalnoy\LaravelCommon\Repo
 */
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

    /**
     * @param Collection $items
     * @param string $relation
     * @param $value
     *
     * @return Collection
     */
    public function fillItemListRelation($items, $relation, $value)
    {
        /** @var Model $item */
        foreach ($items as $item) {
            $item->setRelation($relation, $value);
        }

        return $items;
    }
}