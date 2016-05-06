<?php

namespace Kalnoy\LaravelCommon\DataMapping\Eloquent;

use Kalnoy\LaravelCommon\Contracts\DataMapping\Repository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

abstract class AbstractEloquentRepository implements Repository
{
    /**
     * @var array
     */
    protected $columns;

    /**
     * @var bool
     */
    protected $slugKey;

    /**
     * @var string
     */
    protected $originalKeyAttr;

    /**
     * AbstractEloquentRepository constructor.
     *
     * @param array $columns
     */
    public function __construct(array $columns = [ '*' ])
    {
        $this->columns = $columns;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    abstract public function newModel();

    /**
     * @inheritDoc
     */
    public function retrieveMany(array $keys, $mode = self::ALL)
    {
        $items = array_map(function ($key) use ($mode) {

            return $this->retrieve($key, $mode);

        }, $keys);

        return new EloquentCollection(array_filter(array_combine($keys, $items)));
    }

    /**
     * @param $key
     *
     * @return Model
     */
    protected function createModel($key)
    {
        $model = $this->newModel();

        if ($this->originalKeyAttr) {
            $model->setAttribute($this->originalKeyAttr, $key);
        }

        if ($this->slugKey) {
            $key = str_slug($key);
        }

        $model->setAttribute($this->primaryKey(), $key);

        return $model;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function newQuery()
    {
        return $this->newModel()->newQuery();
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function parseKey($key)
    {
        return is_string($key) ? trim($key) : $key;
    }

    /**
     * @param $mode
     *
     * @return bool
     */
    protected function newModelAllowed($mode)
    {
        return ($mode & self::NON_EXISTING) > 0;
    }

    /**
     * @param $mode
     *
     * @return bool
     */
    protected function existingModelAllowed($mode)
    {
        return ($mode & self::EXISTING) > 0;
    }

    /**
     * @param string $attribute
     *
     * @return $this
     */
    public function saveOriginalKeyTo($attribute)
    {
        $this->originalKeyAttr = $attribute;

        return $this;
    }

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function slugKey($value = true)
    {
        $this->slugKey = $value;

        return $this;
    }
}