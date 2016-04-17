<?php

namespace Kalnoy\LaravelCommon\Repo\Pagination;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;

interface PaginationInterface
{
    /**
     * Paginate items.
     *
     * @param Builder $query
     * @param array $input
     * @param array $columns
     *
     * @return Paginator
     */
    public function paginate(Builder $query, array $input,
                             array $columns = [ '*' ]
    );

}