<?php

namespace Kalnoy\LaravelCommon\Repo;

use Closure;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DbRepository implements RepositoryInterface {

    /**
     * Target model.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * The list of available filters.
     *
     * @var string[]
     */
    protected $filters = [];

    /**
     * The list of relations to load.
     *
     * @var array
     */
    protected $load;

    /**
     * The list of columns to be selected.
     *
     * @var array
     */
    protected $select;

    /**
     * Init repository.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function __construct(Eloquent $model)
    {
        $this->model = $model;
    }

    /**
     * @inheritdoc
     *
     * @param string|array $relation
     *
     * @return $this
     */
    public function load($relation)
    {
        $this->load = is_array($relation) ? $relation : func_get_args();

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @param string|array $columns
     *
     * @return $this
     */
    public function select($columns)
    {
        $this->select = is_array($columns) ? $columns : func_get_args();

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @param mixed $id
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function byId($id)
    {
        return $this->findModel(function ($q) use ($id)
        {
            $q->where($this->model->getKeyName(), '=', $id);
        });
    }

    /**
     * Find an item applying given constraints.
     *
     * @param Closure $constraint
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function findModel(Closure $constraint)
    {
        $query = $this->newQuery($constraint);

        if (null === $model = $query->first())
        {
            throw with(new ModelNotFoundException)->setModel(get_class($this->model));
        }

        return $model;
    }

    /**
     * Default scope that is applied to every query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return void
     */
    protected function scope(Builder $query) {}

    /**
     * Create new query and apply constraints.
     *
     * @param \Closure $constraint
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function newQuery(Closure $constraint = null)
    {
        $query = $this->model->newQuery();

        $this->scope($query);

        if ($constraint) $constraint($query);

        if ($this->load)
        {
            $query->with($this->load);

            $this->load = null;
        }

        if ($this->select)
        {
            $query->select($this->select);

            $this->select = null;
        }

        return $query;
    }

    /**
     * Return new filtered query.
     *
     * @param array    $options
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function filterModels(array $options)
    {
        $query = $this->newQuery();

        return $this->applyFilters($query, $options);
    }

    /**
     * Apply filters to the builder based on provided options.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array                                 $options
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applyFilters(Builder $query, array $options)
    {
        foreach ($this->filters as $filter)
        {
            if (isset($options[$filter]) && ! empty($options[$filter]))
            {
                $method = 'filterBy' . \camel_case($filter);

                $this->$method($query, $options[$filter]);
            }
        }

        return $query;
    }

    /**
     * Get underlying model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModel()
    {
        return $this->model;
    }
}