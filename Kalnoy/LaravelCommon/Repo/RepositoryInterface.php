<?php

namespace Kalnoy\LaravelCommon\Repo;

/**
 * Base repository interface.
 */
interface RepositoryInterface {

    /**
     * Specify a list of relations to be loaded.
     *
     * @param string|array $relation
     *
     * @return $this
     */
    public function load($relation);

    /**
     * Specify a list of column to be selected.
     *
     * @param string|array $columns
     *
     * @return $this
     */
    public function select($columns);

    /**
     * Get all items.
     * 
     * @return \Illuminate\Database\Eloquent\Model[]
     */
    public function all();  

    /**
     * Find a model by a id.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function byId($id);

}