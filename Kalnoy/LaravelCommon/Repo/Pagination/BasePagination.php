<?php

namespace Kalnoy\LaravelCommon\Repo\Pagination;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Base pagination class.
 */
abstract class BasePagination implements PaginationInterface {

    /**
     * Setup paginator.
     *
     * @param \Illuminate\Contracts\Pagination\Paginator $paginator
     * @param array $input
     *
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function setupPaginator(Paginator $paginator, array $input)
    {
        $paginator->appends($input);

        return $paginator;
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

    /**
     * @param $page
     * @param $totalPages
     */
    protected function validatePage($page, $totalPages)
    {
        if ($page < 1 or $totalPages > 0 and $page > $totalPages) throw new HttpException(404);
    }
}