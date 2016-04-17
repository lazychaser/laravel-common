<?php

namespace Kalnoy\LaravelCommon\Repo\Pagination;

use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Eloquent paginator.
 */
class EloquentPaginator extends Lengt {

    /**
     * @inheritdoc
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCollection()
    {
        return $this->items;
    }

}