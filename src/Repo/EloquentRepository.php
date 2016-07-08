<?php

namespace Kalnoy\LaravelCommon\Repo;

use App\Repo\CallsRepo;
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

    /**
     * @param Builder $query
     * @param $attr
     * @param $value
     *
     * @return CallsRepo
     */
    protected function filterByAttr(Builder $query, $attr, $value)
    {
        if ($value) {
            if (is_array($value)) {
                $query->whereIn($attr, $value);
            } else {
                $query->where($attr, $value);
            }
        }

        return $this;
    }
}