<?php

namespace Kalnoy\LaravelCommon\Repo;

use App\Repo\CallsRepo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\LaravelCommon\DataMapping\DataSources\Xml\Collection;
use Symfony\Component\HttpFoundation\ParameterBag;

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
     * @param string $op
     *
     * @return $this
     */
    protected function filterByAttr($query, $attr, $value, $op = '=')
    {
        if (is_null($value)) {
            return $this;
        }

        if (is_array($value)) {
            $query->whereIn($attr, $value);
        } else {
            $query->where($attr, $op, $value);
        }

        return $this;
    }

    /**
     * @param Builder $query
     * @param $attribute
     *
     * @param ParameterBag $input
     *
     * @return CallsRepo
     */
    protected function filterByPeriod(Builder $query, $attribute,
                                      ParameterBag $input
    ) {
        if ($date = $input->get('from')) {
            $query->where($attribute, '>=', $date);
        }

        if ($date = $input->get('to')) {
            $query->where($attribute, '<=', $date);
        }

        return $this;
    }
}