<?php

namespace Kalnoy\LaravelCommon\Repo\Pagination;

use Illuminate\Database\Eloquent\Builder;

class DefaultPagination extends BasePagination {

    /**
     * Apply default pagination.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array                                 $options
     *
     * @return \Kalnoy\LaravelCommon\Repo\Pagination\EloquentPaginator
     */
    public function paginate(Builder $query, array $options)
    {
        $innerQuery = $query->getQuery();

        list($page, $perPage) = $this->getPageSettings($options);

        $total   = (int)$innerQuery->getPaginationCount();

        $innerQuery->forPage($page, $perPage);

        $items = $query->get()->all();

        $paginator = new EloquentPaginator($this->env, $items, $total, $perPage);

        return $this->setupPaginator($paginator, $options);
    }
}