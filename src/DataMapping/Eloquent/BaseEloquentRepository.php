<?php

namespace Kalnoy\LaravelCommon\DataMapping\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

abstract class BaseEloquentRepository extends AbstractEloquentRepository
{
    /**
     * @var array
     */
    protected $loaded = [];

    /**
     * @var array
     */
    public $eager = [];

    /**
     * @inheritDoc
     */
    public function find($key)
    {
        $original = $key;

        if ($this->slugKey) {
            $key = str_slug($key);
        }

        if (empty($key)) return null;

        if ( ! array_key_exists($key, $this->loaded)) {
            $this->preload($original);
        }

        return $this->loaded[$key];
    }

    /**
     * @inheritDoc
     */
    public function retrieve($key, $mode = self::ALL)
    {
        if (empty($key)) throw new \RuntimeException;

        if (false === $model = $this->find($key)) {
            if ( ! $this->newModelAllowed($mode)) {
                return null;
            }

            $model = $this->createModel($key);

            $model->save();

            $this->add($model);

            return $model;
        }

        return $this->existingModelAllowed($mode) ? $model : null;
    }

    /**
     * @param string|array|EloquentCollection $keys
     *
     * @return $this
     */
    public function preload($keys)
    {
        if ($keys instanceof Collection) {
            $keys = $keys->pluck($this->primaryKey())->all();
        } else {
            $keys = (array)$keys;
        }

        if ($this->slugKey) {
            $keys = array_map('str_slug', $keys);
        }

        $keys = array_filter($keys);

        $missing = array_combine($keys, array_pad([ ], count($keys), false));

        $missing = array_diff_key($missing, $this->loaded);

        if ( ! empty($missing)) {
            // Add to the loaded array false values so that models won't be
            // considered missing if they aren't present in database
            $this->loaded += $missing;

            $models = $this->fetchModels(array_keys($missing));

            $this->add($models);
        }

        return $this;
    }

    /**
     * @param array $keys
     *
     * @return EloquentCollection
     */
    protected function fetchModels(array $keys)
    {
        return $this->newQuery()
                    ->whereIn($this->primaryKey(), $keys)
                    ->with($this->eager)
                    ->get($this->processColumns($this->columns));
    }

    /**
     * @param array $columns
     *
     * @return array
     */
    protected function processColumns(array $columns)
    {
        if ($columns == [ '*' ]) return $columns;

        return array_unique(array_merge($columns, [
            $this->primaryKey(), $this->newModel()->getKeyName()
        ]));
    }

    /**
     * @param Model|Collection|array $model
     *
     * @return $this
     */
    public function add($model)
    {
        if ($model instanceof Collection) {
            $model = $model->all();
        } elseif ( ! is_array($model)) {
            $model = [ $model ];
        }

        $pk = $this->primaryKey();

        foreach ((array)$model as $item) {
            $this->loaded[$item->getAttributeValue($pk)] = $item;
        }

        return $this;
    }

    /**
     * @param array $relations
     *
     * @return $this
     */
    public function eager(array $relations)
    {
        $this->eager = $relations;

        return $this;
    }

    /**
     * @return EloquentCollection
     */
    public function getLoaded()
    {
        return (new EloquentCollection($this->loaded))->filter();
    }

}