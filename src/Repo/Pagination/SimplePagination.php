<?php

namespace Kalnoy\LaravelCommon\Repo\Pagination;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;

class SimplePagination extends BasePagination
{
    /**
     * Paginate items.
     *
     * @param Builder $builder
     * @param array $input
     * @param array $columns
     *
     * @return Paginator
     */
    public function paginate(Builder $builder, array $input,
                             array $columns = [ '*' ]
    ) {
        $query = $builder->getQuery();

        $page = max(1, (int)array_get($input, 'page', 1));
        $perPage = $this->getPerPage($builder, $input);

        $query->skip(($page - 1) * $perPage)->take($perPage + 1);

        $paginator = new Paginator($builder->get($columns), $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);

        return $this->setupPaginator($paginator, $input);
    }
}