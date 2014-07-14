<?php

namespace Kalnoy\LaravelCommon\Repo\Pagination;

use Illuminate\Pagination\Factory;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Base pagination class.
 */
abstract class BasePagination implements PaginationInterface {

    /**
     * The pagination environment.
     *
     * @var Illuminate\Pagination\Factory
     */
    protected $env;

    /**
     * Eloquent model.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Init.
     *
     * @param \Illuminate\Database\Eloquent\Model    $model
     * @param \Illuminate\Pagination\Factory $env
     */
    public function __construct(Eloquent $model, Factory $env)
    {
        $this->model = $model;
        $this->env = $env;
    }

    /**
     * Get page settings.
     *
     * @param array $options
     *
     * @return [ $page, $perPage ]
     */
    public function getPageSettings(array $options)
    {
        $page    = max(1, (int)array_get($options, 'page', 1));
        $perPage = (int)array_get($options, 'per_page', $this->model->getPerPage());

        return [ $page, $perPage ];
    }

    /**
     * Setup paginator.
     *
     * @param \Illuminate\Pagination\Paginator $paginator
     * @param array     $options
     *
     * @return \Illuminate\Pagination\Paginator
     */
    public function setupPaginator(Paginator $paginator, array $options)
    {
        unset($options['page']);
        
        $paginator->appends($options);

        return $paginator->setupPaginationContext();
    }
}   