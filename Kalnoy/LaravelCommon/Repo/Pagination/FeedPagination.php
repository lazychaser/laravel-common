<?php

namespace Kalnoy\LaravelCommon\Repo\Pagination;

use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;

class FeedPagination extends BasePagination {

    /**
     * Paginate items.
     *
     * @param Builder $builder
     * @param array $input
     * @param array $columns
     *
     * @return FeedPaginator
     */
    public function paginate(Builder $builder, array $input, array $columns = [ '*' ])
    {
        $query = $builder->getQuery();

        $perPage = $this->getPerPage($builder, $input);
        $column = array_get($input, 'column', 'created_at');

        $query->orderBy($column, 'desc');

        if ($timestamp = array_get($input, 'timestamp'))
        {
            $query->where($column, '<', Carbon::createFromTimestamp($timestamp));
        }

        $query->take($perPage + 1);

        return new FeedPaginator($builder->get(), $perPage, $timestamp, $column);
    }

}