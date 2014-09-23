<?php

namespace Kalnoy\LaravelCommon\Repo\Pagination;

use Illuminate\Database\Eloquent\Builder;

interface PaginationInterface {

    /**
     * Paginate items.
     *
     * @param Builder $query
     * @param array   $input
     * @param array   $columns
     *
     * @return \Illuminate\Support\Contracts\ArrayableInterface
     */
    public function paginate(Builder $query, array $input, array $columns = ['*']);

}