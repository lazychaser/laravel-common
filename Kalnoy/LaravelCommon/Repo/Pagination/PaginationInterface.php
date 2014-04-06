<?php

namespace Kalnoy\LaravelCommon\Repo\Pagination;

use Illuminate\Database\Eloquent\Builder;

/**
 * PaginatorInterface
 */
interface PaginationInterface {

    /**
     * Paginate items.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array                                 $options
     *
     * @return \App\Service\Pagination\PaginatedInterface
     */
    public function paginate(Builder $query, array $options);

}