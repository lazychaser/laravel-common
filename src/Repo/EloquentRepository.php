<?php

namespace Kalnoy\LaravelCommon\Repo;

use App\Repo\CallsRepo;
use Carbon\Carbon;
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
     * @param mixed $query
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
     * @param mixed $query
     * @param $attribute
     *
     * @param ParameterBag $input
     *
     * @return $this
     */
    protected function filterByPeriod($query, $attribute, ParameterBag $input)
    {
        if ($date = $this->getPeriodFrom($input)) {
            $query->where($attribute, '>=', $date);
        }

        if ($date = $this->getPeriodTo($input)) {
            $query->where($attribute, '<=', $date);
        }

        return $this;
    }

    /**
     * @param ParameterBag $input
     *
     * @return Carbon|null
     */
    protected function getPeriodFrom(ParameterBag $input)
    {
        if ($date = $input->get('from')) {
            try {
                return Carbon::createFromFormat('Y-m-d', $date, 'Europe/Moscow')
                              ->setTime(0, 0, 0);
            }

            catch (\Exception $e) {}
        }

        return null;
    }

    /**
     * @param ParameterBag $input
     *
     * @return Carbon|null
     */
    protected function getPeriodTo(ParameterBag $input)
    {
        if ($date = $input->get('to')) {
            try {
                return Carbon::createFromFormat('Y-m-d', $date, 'Europe/Moscow')
                              ->setTime(23, 59, 59);
            }

            catch (\Exception $e) {}
        }

        return null;
    }
}