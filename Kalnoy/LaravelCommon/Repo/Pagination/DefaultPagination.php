<?php

namespace Kalnoy\LaravelCommon\Repo\Pagination;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class DefaultPagination extends BasePagination {

    /**
     * Apply default pagination.
     *
     * @param Builder $builder
     * @param array   $input
     * @param array   $columns
     *
     * @return LengthAwarePaginator
     */
    public function paginate(Builder $builder, array $input, array $columns = ['*'])
    {
        $query = $builder->getQuery();

        $page = max(1, (int)array_get($input, 'page', 1));
        $perPage = $this->getPerPage($builder, $input);

        $total = (int)$query->getCountForPagination();

        $this->validatePage($page, (int)ceil((float)$total / $perPage));

        $query->forPage($page, $perPage);

        $items = $builder->get();

        $paginator = new LengthAwarePaginator($items, $total, $perPage, $page);

        return $this->setupPaginator($paginator, $input);
    }

}