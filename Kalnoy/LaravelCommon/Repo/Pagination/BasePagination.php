<?php

namespace Kalnoy\LaravelCommon\Repo\Pagination;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;

/**
 * Base pagination class.
 */
abstract class BasePagination implements PaginationInterface {

    /**
     * Setup paginator.
     *
     * @param \Illuminate\Pagination\Paginator $paginator
     * @param array $input
     *
     * @return \Illuminate\Pagination\Paginator
     */
    public function setupPaginator(Paginator $paginator, array $input)
    {
        $paginator->appends($input);

        return $paginator->setupPaginationContext();
    }

    /**
     * @param $query
     *
     * @return \Illuminate\Pagination\Factory
     */
    protected function getFactory($query)
    {
        $factory = $query->getConnection()->getPaginator();

        return $factory;
    }

    /**
     * @param Builder $builder
     * @param array   $input
     *
     * @return int
     */
    protected function getPerPage(Builder $builder, array $input)
    {
        return (int)array_get($input, 'per_page') ?: $builder->getModel()->getPerPage();
    }
}