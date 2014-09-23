<?php

namespace Kalnoy\LaravelCommon\Repo\Pagination;

use Illuminate\Database\Eloquent\Builder;

class DefaultPagination extends BasePagination {

    /**
     * Apply default pagination.
     *
     * @param Builder $builder
     * @param array   $input
     * @param array   $columns
     *
     * @return \Kalnoy\LaravelCommon\Repo\Pagination\EloquentPaginator
     */
    public function paginate(Builder $builder, array $input, array $columns = ['*'])
    {
        $query = $builder->getQuery();
        $factory = $this->getFactory($query);

        $page = $factory->getCurrentPage();
        $perPage = $this->getPerPage($builder, $input);

        $total = (int)$query->getPaginationCount();

        $query->forPage($page, $perPage);

        $items = $builder->get()->all();

        $paginator = new EloquentPaginator($factory, $items, $total, $perPage);

        return $this->setupPaginator($paginator, $input);
    }
}